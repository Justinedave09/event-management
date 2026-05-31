<?php
$safeWebRoot = defined('WEB_ROOT') ? rtrim(WEB_ROOT, '/') : '';
?>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">
      <i class="fa fa-comments"></i> AI Chat
      <small style="font-size:11px; color:#aaa; margin-left:8px;">
        Powered by Groq &mdash; <?php echo defined('GROQ_MODEL') ? htmlspecialchars(GROQ_MODEL) : 'llama-3.3-70b-versatile'; ?>
      </small>
    </h3>
    <div class="pull-right" style="display:flex; gap:6px;">
      <button id="chatClearBtn" class="btn btn-xs btn-warning" title="Clear conversation">
        <i class="fa fa-trash"></i> Clear
      </button>
      <!-- <button id="chatDiagnosticBtn" class="btn btn-xs btn-default" title="Run diagnostics">
        <i class="fa fa-stethoscope"></i> Diagnostics
      </button> -->
    </div>
  </div>

  <div class="box-body" style="padding:10px;">

    <!-- Error Alert -->
    <div id="chatConsoleAlert" class="alert alert-danger"
         style="display:none; margin-bottom:10px; padding:10px;
                font-size:12px; font-family:monospace; white-space:pre-wrap;">
    </div>

    <!-- Message Window -->
    <div id="chatMessages"
         style="min-height:380px; max-height:440px; overflow-y:auto;
                border:1px solid #ddd; background:#f9f9f9;
                padding:14px; border-radius:6px;">

      <div id="chatPlaceholder" style="color:#999; text-align:center; padding-top:60px;">
        <i class="fa fa-commenting-o"
           style="font-size:40px; color:#ccc; display:block; margin-bottom:12px;"></i>
        <strong style="font-size:14px;">AI Assistant Ready</strong><br>
        <span style="font-size:12px;">Type your question or click <i class="fa fa-bolt" style="color:#f39c12;"></i> for quick questions.</span>
      </div>

    </div>

    <!-- Typing Indicator -->
    <div id="chatTyping"
         style="display:none; padding:8px 4px; font-size:12px; color:#888;">
      <i class="fa fa-circle" style="font-size:8px; animation: blink 1s infinite;"></i>
      <i class="fa fa-circle" style="font-size:8px; animation: blink 1s infinite 0.2s;"></i>
      <i class="fa fa-circle" style="font-size:8px; animation: blink 1s infinite 0.4s;"></i>
      &nbsp;AI is thinking...
    </div>

    <!-- ═══════════════════════════════════════════ -->
    <!-- SMART INPUT WITH INTEGRATED FAQ DROPDOWN   -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="smart-input-wrapper" style="margin-top:8px; position:relative;">
      <div class="smart-input-group">

        <!-- FAQ trigger button (left) -->
        <button id="faqToggleBtn" class="smart-btn-left" type="button" title="Quick Questions (Ctrl + /)">
          <i class="fa fa-bolt" style="color:#f39c12;"></i>
        </button>

        <!-- Input field (middle) -->
        <input id="chatInput"
               type="text"
               class="smart-input"
               placeholder="Type your message or click ⚡ for quick questions..."
               autocomplete="off"
               maxlength="2000">

        <!-- Send button (right) -->
        <button id="chatSend" class="smart-btn-right">
          <i class="fa fa-paper-plane"></i> Send
        </button>

      </div>

      <!-- Dropdown panel (floats above the input) -->
      <div id="faqDropdown" class="faq-dropdown">
        <div class="faq-dropdown-header">
          <input id="faqSearch" type="text" class="form-control input-sm"
                 placeholder="🔍 Search questions..." autocomplete="off">
        </div>
        <div id="faqList" class="faq-dropdown-list">
          <div class="faq-empty">
            <i class="fa fa-spinner fa-spin"></i> Loading FAQs...
          </div>
        </div>
      </div>

    </div>

    <div style="text-align:right; font-size:11px; color:#bbb; margin-top:4px;">
      <span id="charCount">0</span> / 2000
    </div>

  </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════ -->
<!-- STYLES                                                                -->
<!-- ════════════════════════════════════════════════════════════════════ -->
<style>
  @keyframes blink {
    0%, 100% { opacity: 0.2; }
    50%       { opacity: 1;   }
  }

  /* ─── Code blocks in chat ──────────────────── */
  #chatMessages pre {
    background: #2b2b2b;
    color: #f8f8f2;
    padding: 10px 14px;
    border-radius: 6px;
    overflow-x: auto;
    font-size: 12px;
    margin: 6px 0;
    white-space: pre-wrap;
    word-break: break-word;
  }

  #chatMessages code {
    background: #e8e8e8;
    color: #c7254e;
    padding: 1px 5px;
    border-radius: 3px;
    font-size: 12px;
  }

  #chatMessages pre code {
    background: transparent;
    color: inherit;
    padding: 0;
  }

  /* ─── Chat bubbles ─────────────────────────── */
  .chat-bubble-user {
    display: inline-block;
    max-width: 78%;
    padding: 9px 14px;
    border-radius: 18px 18px 4px 18px;
    font-size: 13px;
    line-height: 1.55;
    background: #337ab7;
    color: #fff;
    word-break: break-word;
  }

  .chat-bubble-ai {
    display: inline-block;
    max-width: 78%;
    padding: 9px 14px;
    border-radius: 18px 18px 18px 4px;
    font-size: 13px;
    line-height: 1.55;
    background: #ffffff;
    color: #333;
    border: 1px solid #e0e0e0;
    word-break: break-word;
  }

  .chat-timestamp {
    font-size: 10px;
    color: #bbb;
    margin-top: 3px;
  }

  /* ════════════════════════════════════════════ */
  /* Smart Input Group (Flexbox Layout)           */
  /* ════════════════════════════════════════════ */
  .smart-input-group {
    display: flex;
    width: 100%;
    align-items: stretch;
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid #ccc;
    background: #fff;
    transition: border-color 0.2s, box-shadow 0.2s;
  }

  .smart-input-group:focus-within {
    border-color: #66afe9;
    box-shadow: 0 0 0 2px rgba(102, 175, 233, 0.2);
  }

  .smart-btn-left,
  .smart-btn-right {
    border: none;
    border-radius: 0;
    margin: 0;
    padding: 8px 14px;
    flex-shrink: 0;
    height: auto;
    outline: none;
    cursor: pointer;
    font-size: 13px;
    transition: background 0.15s;
  }

  .smart-btn-left {
    border-right: 1px solid #ddd;
    background: #f5f5f5;
    color: #333;
  }

  .smart-btn-left:hover {
    background: #e8e8e8;
  }

  .smart-btn-left.active {
    background: #f39c12;
    color: #fff;
    border-right-color: #e67e22;
  }

  .smart-btn-left.active i {
    color: #fff !important;
  }

  .smart-btn-right {
    background: #337ab7;
    color: #fff;
    font-weight: 500;
    border-left: 1px solid #2e6da4;
    min-width: 90px;
  }

  .smart-btn-right:hover:not(:disabled) {
    background: #286090;
  }

  .smart-btn-right:disabled {
    background: #aaa;
    cursor: not-allowed;
  }

  .smart-input {
    flex: 1;
    border: none;
    border-radius: 0;
    box-shadow: none;
    outline: none;
    padding: 8px 12px;
    font-size: 13px;
    min-width: 0;
    background: transparent;
  }

  .smart-input:focus {
    border: none;
    box-shadow: none;
    outline: none;
  }

  /* ════════════════════════════════════════════ */
  /* FAQ Dropdown Styles                          */
  /* ════════════════════════════════════════════ */
  .faq-dropdown {
    display: none;
    position: absolute;
    bottom: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-shadow: 0 -4px 16px rgba(0,0,0,0.18);
    max-height: 350px;
    overflow: hidden;
    z-index: 1000;
    flex-direction: column;
  }

  .faq-dropdown.open {
    display: flex;
  }

  .faq-dropdown-header {
    padding: 8px;
    border-bottom: 1px solid #eee;
    background: #f9f9f9;
    flex-shrink: 0;
  }

  .faq-dropdown-list {
    overflow-y: auto;
    flex: 1;
    max-height: 290px;
  }

  .faq-category {
    padding: 6px 12px;
    background: #f0f0f0;
    font-size: 11px;
    font-weight: bold;
    color: #555;
    text-transform: uppercase;
    border-bottom: 1px solid #e0e0e0;
    position: sticky;
    top: 0;
    z-index: 1;
  }

  .faq-item {
    padding: 9px 14px;
    cursor: pointer;
    font-size: 13px;
    color: #333;
    border-bottom: 1px solid #f5f5f5;
    transition: background 0.15s;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .faq-item:hover,
  .faq-item.highlighted {
    background: #e8f4fd;
    color: #1a73e8;
  }

  .faq-item:last-child {
    border-bottom: none;
  }

  .faq-item-icon {
    color: #aaa;
    flex-shrink: 0;
  }

  .faq-item:hover .faq-item-icon,
  .faq-item.highlighted .faq-item-icon {
    color: #1a73e8;
  }

  .faq-empty {
    padding: 20px;
    text-align: center;
    color: #999;
    font-size: 13px;
  }
</style>

<!-- ════════════════════════════════════════════════════════════════════ -->
<!-- JAVASCRIPT                                                            -->
<!-- ════════════════════════════════════════════════════════════════════ -->
<script>
(function () {
  'use strict';

  const apiUrl     = <?php echo json_encode($safeWebRoot . '/api/chat.php'); ?>;
  const faqsApiUrl = <?php echo json_encode($safeWebRoot . '/api/faqs_list.php'); ?>;

  let allFaqs = [];
  let highlightedIndex = -1;

  // ────────────────────────────────────────────────────────
  // Helpers
  // ────────────────────────────────────────────────────────

  function escapeHtml(text) {
    return String(text).replace(/[&<>"']/g, function (c) {
      return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c];
    });
  }

  function getTime() {
    const now = new Date();
    let h = now.getHours(), m = now.getMinutes();
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return h + ':' + (m < 10 ? '0' + m : m) + ' ' + ampm;
  }

  function formatMarkdown(text) {
    let html = escapeHtml(text);

    // Fenced code blocks
    html = html.replace(/```(\w*)\n?([\s\S]*?)```/g, function (_, lang, code) {
      const attr = lang ? ' data-lang="' + escapeHtml(lang) + '"' : '';
      return '<pre' + attr + '><code>' + code + '</code></pre>';
    });

    // Inline code
    html = html.replace(/`([^`]+)`/g, '<code>$1</code>');

    // Bold
    html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/__(.+?)__/g,     '<strong>$1</strong>');

    // Italic
    html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
    html = html.replace(/_(.+?)_/g,   '<em>$1</em>');

    // Unordered list
    html = html.replace(/(^|\n)- (.+)/g, '$1<li>$2</li>');
    html = html.replace(/(<li>[\s\S]+?<\/li>)/g, '<ul style="margin:6px 0 6px 16px;">$1</ul>');

    // Numbered list
    html = html.replace(/(^|\n)\d+\. (.+)/g, '$1<li>$2</li>');

    // Newlines
    html = html.replace(/\n/g, '<br>');

    return html;
  }

  function displayUIError(message, details) {
    const box = document.getElementById('chatConsoleAlert');
    if (!box) return;
    box.style.display = 'block';
    box.innerHTML =
      '<i class="fa fa-exclamation-triangle"></i> <strong>Error:</strong> ' +
      escapeHtml(String(message)) +
      (details ? '<br><small>' + escapeHtml(String(details)) + '</small>' : '');
  }

  function hideUIError() {
    const box = document.getElementById('chatConsoleAlert');
    if (box) box.style.display = 'none';
  }

  // ────────────────────────────────────────────────────────
  // FAQ Dropdown
  // ────────────────────────────────────────────────────────

  function loadFAQs() {
    fetch(faqsApiUrl)
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function (data) {
        allFaqs = data.faqs || [];
        renderFAQs(allFaqs);
      })
      .catch(function (err) {
        const list = document.getElementById('faqList');
        if (list) {
          list.innerHTML = '<div class="faq-empty">⚠️ Failed to load FAQs</div>';
        }
        console.error('FAQ load failed:', err);
      });
  }

  function renderFAQs(faqs) {
    const list = document.getElementById('faqList');
    if (!list) return;

    highlightedIndex = -1;

    if (faqs.length === 0) {
      list.innerHTML = '<div class="faq-empty">No matching questions found</div>';
      return;
    }

    // Group by category
    const grouped = {};
    faqs.forEach(function (faq) {
      const cat = faq.category || 'General';
      if (!grouped[cat]) grouped[cat] = [];
      grouped[cat].push(faq);
    });

    let html = '';
    Object.keys(grouped).sort().forEach(function (category) {
      html += '<div class="faq-category">📁 ' + escapeHtml(category) + '</div>';
      grouped[category].forEach(function (faq) {
        html += '<div class="faq-item" data-question="' + escapeHtml(faq.question) + '">' +
                  '<i class="fa fa-question-circle faq-item-icon"></i>' +
                  '<span>' + escapeHtml(faq.question) + '</span>' +
                '</div>';
      });
    });

    list.innerHTML = html;

    // Attach click handlers
    list.querySelectorAll('.faq-item').forEach(function (item) {
      item.addEventListener('click', function () {
        const question = this.dataset.question;
        selectFAQ(question);
      });
    });
  }

  function selectFAQ(question) {
    const input = document.getElementById('chatInput');
    if (!input) return;

    input.value = question;
    updateCharCount(question);
    closeDropdown();
    input.focus();

    // Brief visual feedback
    input.style.background = '#fff9e6';
    setTimeout(function () { input.style.background = ''; }, 500);
  }

  function openDropdown() {
    const dropdown = document.getElementById('faqDropdown');
    const btn      = document.getElementById('faqToggleBtn');
    const search   = document.getElementById('faqSearch');
    if (!dropdown) return;

    dropdown.classList.add('open');
    if (btn) btn.classList.add('active');
    if (search) {
      search.value = '';
      renderFAQs(allFaqs);
      setTimeout(function () { search.focus(); }, 50);
    }
  }

  function closeDropdown() {
    const dropdown = document.getElementById('faqDropdown');
    const btn      = document.getElementById('faqToggleBtn');
    if (!dropdown) return;

    dropdown.classList.remove('open');
    if (btn) btn.classList.remove('active');
    highlightedIndex = -1;
  }

  function toggleDropdown() {
    const dropdown = document.getElementById('faqDropdown');
    if (!dropdown) return;
    if (dropdown.classList.contains('open')) {
      closeDropdown();
    } else {
      openDropdown();
    }
  }

  function filterFAQs(searchTerm) {
    const term = searchTerm.toLowerCase().trim();
    if (!term) {
      renderFAQs(allFaqs);
      return;
    }
    const filtered = allFaqs.filter(function (faq) {
      return faq.question.toLowerCase().includes(term) ||
             faq.category.toLowerCase().includes(term);
    });
    renderFAQs(filtered);
  }

  function navigateDropdown(direction) {
    const items = document.querySelectorAll('.faq-item');
    if (items.length === 0) return;

    // Remove previous highlight
    items.forEach(function (i) { i.classList.remove('highlighted'); });

    highlightedIndex += direction;
    if (highlightedIndex < 0) highlightedIndex = items.length - 1;
    if (highlightedIndex >= items.length) highlightedIndex = 0;

    const item = items[highlightedIndex];
    item.classList.add('highlighted');
    item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
  }

  function selectHighlighted() {
    const items = document.querySelectorAll('.faq-item');
    if (highlightedIndex >= 0 && items[highlightedIndex]) {
      selectFAQ(items[highlightedIndex].dataset.question);
    }
  }

  // ────────────────────────────────────────────────────────
  // Message Rendering
  // ────────────────────────────────────────────────────────

  function appendMessage(role, rawText) {
    const placeholder = document.getElementById('chatPlaceholder');
    if (placeholder) placeholder.remove();

    const container = document.getElementById('chatMessages');
    if (!container) return;

    const isUser  = (role === 'user');
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'margin-bottom:14px; text-align:' + (isUser ? 'right' : 'left') + ';';

    const row = document.createElement('div');
    row.style.cssText =
      'display:flex; align-items:flex-end; justify-content:' +
      (isUser ? 'flex-end' : 'flex-start') + '; gap:8px;';

    if (!isUser) {
      const avatar = document.createElement('div');
      avatar.style.cssText =
        'width:28px; height:28px; border-radius:50%; background:#337ab7; ' +
        'color:#fff; display:flex; align-items:center; justify-content:center; ' +
        'font-size:13px; flex-shrink:0;';
      avatar.innerHTML = '<i class="fa fa-robot"></i>';
      row.appendChild(avatar);
    }

    const bubble = document.createElement('div');
    bubble.className = isUser ? 'chat-bubble-user' : 'chat-bubble-ai';
    bubble.innerHTML  = formatMarkdown(rawText);
    row.appendChild(bubble);

    if (isUser) {
      const avatar = document.createElement('div');
      avatar.style.cssText =
        'width:28px; height:28px; border-radius:50%; background:#5a5a5a; ' +
        'color:#fff; display:flex; align-items:center; justify-content:center; ' +
        'font-size:13px; flex-shrink:0;';
      avatar.innerHTML = '<i class="fa fa-user"></i>';
      row.appendChild(avatar);
    }

    wrapper.appendChild(row);

    const ts = document.createElement('div');
    ts.className     = 'chat-timestamp';
    ts.style.cssText = 'text-align:' + (isUser ? 'right' : 'left') +
                       '; padding-' + (isUser ? 'right' : 'left') + ':36px;';
    ts.textContent   = getTime();
    wrapper.appendChild(ts);

    container.appendChild(wrapper);
    container.scrollTop = container.scrollHeight;
  }

  // ────────────────────────────────────────────────────────
  // Loading State
  // ────────────────────────────────────────────────────────

  function setLoading(state) {
    const btn    = document.getElementById('chatSend');
    const input  = document.getElementById('chatInput');
    const typing = document.getElementById('chatTyping');
    const faqBtn = document.getElementById('faqToggleBtn');

    if (btn) {
      btn.disabled  = state;
      btn.innerHTML = state
        ? '<i class="fa fa-spinner fa-spin"></i> Sending'
        : '<i class="fa fa-paper-plane"></i> Send';
    }

    if (input)  input.disabled  = state;
    if (faqBtn) faqBtn.disabled = state;
    if (typing) typing.style.display = state ? 'block' : 'none';
  }

  // ────────────────────────────────────────────────────────
  // Send Message
  // ────────────────────────────────────────────────────────

  function sendMessage() {
    const input = document.getElementById('chatInput');
    if (!input) return;

    const message = input.value.trim();
    if (!message) return;

    hideUIError();
    closeDropdown();
    input.value = '';
    updateCharCount('');
    appendMessage('user', message);
    setLoading(true);

    fetch(apiUrl, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ message: message }),
    })
    .then(function (response) {
      if (!response.ok) {
        return response.text().then(function (body) {
          throw new Error('HTTP ' + response.status + ': ' + body);
        });
      }
      return response.json();
    })
    .then(function (data) {
      if (data.error) {
        appendMessage('assistant', '⚠️ ' + data.error + (data.detail ? '\n\n' + data.detail : ''));
        displayUIError(data.error, data.detail || '');
        return;
      }
      const reply = data.reply || '(No response from server)';
      appendMessage('assistant', reply);
    })
    .catch(function (err) {
      appendMessage('assistant', '⚠️ Could not reach the server. Please try again.');
      displayUIError(err.message);
    })
    .finally(function () {
      setLoading(false);
      const inp = document.getElementById('chatInput');
      if (inp) inp.focus();
    });
  }

  // ────────────────────────────────────────────────────────
  // Clear Chat
  // ────────────────────────────────────────────────────────

  function clearChat() {
    if (!confirm('Clear the entire conversation history?')) return;

    fetch(apiUrl, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ reset: true, message: '' }),
    })
    .then(function () {
      const container = document.getElementById('chatMessages');
      if (!container) return;
      container.innerHTML =
        '<div id="chatPlaceholder" style="color:#999; text-align:center; padding-top:60px;">' +
        '<i class="fa fa-commenting-o" style="font-size:40px; color:#ccc; display:block; margin-bottom:12px;"></i>' +
        '<strong style="font-size:14px;">AI Assistant Ready</strong><br>' +
        '<span style="font-size:12px;">Type your question or click <i class="fa fa-bolt" style="color:#f39c12;"></i> for quick questions.</span>' +
        '</div>';
      hideUIError();
    })
    .catch(function (err) {
      displayUIError('Could not clear chat: ' + err.message);
    });
  }

  // ────────────────────────────────────────────────────────
  // Character Counter
  // ────────────────────────────────────────────────────────

  function updateCharCount(value) {
    const counter = document.getElementById('charCount');
    if (!counter) return;
    const len = String(value).length;
    counter.textContent = len;
    counter.style.color = len > 1800 ? '#e74c3c' : '#bbb';
  }

  // ────────────────────────────────────────────────────────
  // Diagnostics
  // ────────────────────────────────────────────────────────

  function runDiagnostics() {
    const lines = [
      '=== AI Chat Diagnostics ===',
      'API URL    : ' + apiUrl,
      'FAQs URL   : ' + faqsApiUrl,
      'FAQs loaded: ' + allFaqs.length + ' questions',
      'Input box  : ' + (document.getElementById('chatInput')    ? 'OK' : 'MISSING'),
      'Send btn   : ' + (document.getElementById('chatSend')     ? 'OK' : 'MISSING'),
      'FAQ btn    : ' + (document.getElementById('faqToggleBtn') ? 'OK' : 'MISSING'),
      'Messages   : ' + (document.getElementById('chatMessages') ? 'OK' : 'MISSING'),
      'Protocol   : ' + window.location.protocol,
      'Origin     : ' + window.location.origin,
    ];
    alert(lines.join('\n'));
  }

  // ────────────────────────────────────────────────────────
  // Global Error Catch
  // ────────────────────────────────────────────────────────

  window.addEventListener('error', function (e) {
    displayUIError(e.message, e.filename + ' (line ' + e.lineno + ')');
  });

  // ────────────────────────────────────────────────────────
  // Bootstrap (DOM Ready)
  // ────────────────────────────────────────────────────────

  document.addEventListener('DOMContentLoaded', function () {
    console.log('[AI Chat] ready — API:', apiUrl);

    const diagBtn    = document.getElementById('chatDiagnosticBtn');
    const clearBtn   = document.getElementById('chatClearBtn');
    const sendBtn    = document.getElementById('chatSend');
    const input      = document.getElementById('chatInput');
    const faqBtn     = document.getElementById('faqToggleBtn');
    const faqSearch  = document.getElementById('faqSearch');

    if (diagBtn)  diagBtn.addEventListener('click', runDiagnostics);
    if (clearBtn) clearBtn.addEventListener('click', clearChat);
    if (sendBtn)  sendBtn.addEventListener('click',  sendMessage);

    if (faqBtn) {
      faqBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        toggleDropdown();
      });
    }

    // FAQ search input
    if (faqSearch) {
      faqSearch.addEventListener('input', function () {
        filterFAQs(this.value);
      });

      faqSearch.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          closeDropdown();
          if (input) input.focus();
        } else if (e.key === 'ArrowDown') {
          e.preventDefault();
          navigateDropdown(1);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          navigateDropdown(-1);
        } else if (e.key === 'Enter') {
          e.preventDefault();
          selectHighlighted();
        }
      });
    }

    // Main input listeners
    if (input) {
      input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          sendMessage();
        }
        // Ctrl + / opens FAQ dropdown
        if ((e.ctrlKey || e.metaKey) && e.key === '/') {
          e.preventDefault();
          openDropdown();
        }
      });

      input.addEventListener('input', function () {
        updateCharCount(this.value);
      });

      input.focus();
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
      const wrapper = document.querySelector('.smart-input-wrapper');
      if (wrapper && !wrapper.contains(e.target)) {
        closeDropdown();
      }
    });

    if (!input || !sendBtn) {
      displayUIError('UI elements missing — check that element IDs have not been changed.');
    }

    // Load FAQs on page ready
    loadFAQs();
  });

}());
</script>