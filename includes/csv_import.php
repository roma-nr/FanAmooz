<?php

declare(strict_types=1);

/**
 * @return array{imported:int, skipped:int, errors:array<int,string>}
 */
function import_students_csv(string $filePath): array
{
    $handle = fopen($filePath, 'r');
    if ($handle === false) {
        throw new RuntimeException('فایل CSV قابل خواندن نیست.');
    }

    $bom = fread($handle, 3);
    if ($bom !== "\xEF\xBB\xBF") {
        rewind($handle);
    }

    $header = fgetcsv($handle);
    if ($header === false) {
        fclose($handle);
        throw new RuntimeException('فایل CSV خالی است.');
    }

    $header = array_map(static fn ($h) => mb_strtolower(trim((string) $h)), $header);
    $map = [];
    foreach ($header as $i => $col) {
        $aliases = [
            'full_name' => ['full_name', 'name', 'نام', 'نام_کامل'],
            'student_code' => ['student_code', 'code', 'کد_دانشجویی', 'کد دانشجویی'],
            'national_id' => ['national_id', 'nationalid', 'کد_ملی', 'کد ملی'],
            'institution_id' => ['institution_id', 'institution', 'دانشکده_id'],
            'province_name' => ['province_name', 'province', 'استان'],
            'institution_name' => ['institution_name', 'college', 'دانشکده', 'دانشگاه'],
            'phone' => ['phone', 'mobile', 'تلفن', 'موبایل'],
        ];
        foreach ($aliases as $key => $names) {
            if (in_array($col, $names, true)) {
                $map[$key] = $i;
            }
        }
    }

    foreach (['full_name', 'student_code', 'national_id'] as $required) {
        if (!isset($map[$required])) {
            fclose($handle);
            throw new RuntimeException('ستون‌های الزامی: full_name, student_code, national_id');
        }
    }

    $hasInstId = isset($map['institution_id']);
    $hasNames = isset($map['province_name']) && isset($map['institution_name']);
    if (!$hasInstId && !$hasNames) {
        fclose($handle);
        throw new RuntimeException('ستون institution_id یا province_name + institution_name لازم است.');
    }

    $imported = 0;
    $skipped = 0;
    $errors = [];
    $line = 1;

    while (($row = fgetcsv($handle)) !== false) {
        $line++;
        if ($row === [null] || trim(implode('', $row)) === '') {
            continue;
        }

        try {
            $fullName = trim($row[$map['full_name']] ?? '');
            $studentCode = trim($row[$map['student_code']] ?? '');
            $nationalId = trim($row[$map['national_id']] ?? '');
            $phone = isset($map['phone']) ? trim($row[$map['phone']] ?? '') : null;

            if ($fullName === '' || $studentCode === '' || $nationalId === '') {
                throw new InvalidArgumentException('فیلدهای خالی');
            }

            if ($hasInstId) {
                $institutionId = (int) ($row[$map['institution_id']] ?? 0);
            } else {
                $institutionId = resolve_institution_id(
                    trim($row[$map['province_name']] ?? ''),
                    trim($row[$map['institution_name']] ?? '')
                );
            }

            if ($institutionId <= 0) {
                throw new InvalidArgumentException('دانشکده نامعتبر');
            }

            create_student_user($fullName, $studentCode, $nationalId, $institutionId, $phone ?: null);
            $imported++;
        } catch (PDOException $e) {
            $skipped++;
            $errors[$line] = str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'uk_student')
                ? 'رکورد تکراری (کد دانشجویی/کد ملی)'
                : 'خطای پایگاه داده';
        } catch (Throwable $e) {
            $skipped++;
            $errors[$line] = $e->getMessage();
        }
    }

    fclose($handle);

    return ['imported' => $imported, 'skipped' => $skipped, 'errors' => $errors];
}

function resolve_institution_id(string $provinceName, string $institutionName): int
{
    $stmt = db()->prepare(
        'SELECT i.id FROM institutions i
         JOIN provinces p ON p.id = i.province_id
         WHERE p.name = ? AND i.name = ? AND i.is_active = 1 LIMIT 1'
    );
    $stmt->execute([$provinceName, $institutionName]);
    $row = $stmt->fetch();

    return $row ? (int) $row['id'] : 0;
}
