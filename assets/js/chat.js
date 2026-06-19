(function () {
  function scrollChatToBottom(box) {
    if (!box) return;
    var body = box.querySelector('.card-body') || box;
    body.scrollTop = body.scrollHeight;
  }

  function appendMessage(body, msg) {
    var empty = body.querySelector('.chat-empty-hint');
    if (empty) empty.remove();

    var wrap = document.createElement('div');
    wrap.className = 'chat-bubble-wrap ' + (msg.is_mine ? 'chat-mine' : 'chat-theirs');
    wrap.setAttribute('data-msg-id', msg.id);

    var meta = document.createElement('div');
    meta.className = 'chat-meta small';
    meta.innerHTML = msg.sender_name + ' <span class="text-muted">' + msg.created_at + '</span>';

    var bubble = document.createElement('div');
    bubble.className = 'chat-bubble';
    bubble.innerHTML = msg.body.replace(/\n/g, '<br>');

    wrap.appendChild(meta);
    wrap.appendChild(bubble);

    if (msg.file_url) {
      var link = document.createElement('a');
      link.href = msg.file_url;
      link.className = 'chat-file-link small';
      link.target = '_blank';
      link.rel = 'noopener';
      link.innerHTML = '<i class="bi bi-paperclip"></i> دانلود پیوست';
      wrap.appendChild(link);
    }

    body.appendChild(wrap);
  }

  document.querySelectorAll('.chat-panel').forEach(function (panel) {
    var box = panel.querySelector('#chatMessagesBox');
    var apiUrl = panel.getAttribute('data-api-url');
    var lastId = parseInt(panel.getAttribute('data-last-id') || '0', 10);
    var body = box ? box.querySelector('.card-body') : null;

    scrollChatToBottom(box);

    if (!apiUrl || !body) return;

    setInterval(function () {
      fetch(apiUrl + '?after_id=' + lastId)
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (!data.messages || !data.messages.length) return;
          data.messages.forEach(function (msg) {
            if (document.querySelector('[data-msg-id="' + msg.id + '"]')) return;
            appendMessage(body, msg);
            lastId = Math.max(lastId, msg.id);
          });
          panel.setAttribute('data-last-id', String(lastId));
          scrollChatToBottom(box);
        })
        .catch(function () {});
    }, 12000);
  });
})();
