/* global Quill */
window.initQuillEditors = function(container) {
    const root = container || document;
    root.querySelectorAll('textarea[data-quill]').forEach(function(textarea) {
        if (textarea._quillInit) return;
        textarea._quillInit = true;

        const toolbar = (textarea.dataset.quill === 'full')
            ? [
                [{ header: [2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean']
              ]
            : [
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean']
              ];

        const wrapper = document.createElement('div');
        wrapper.className = 'ql-wrapper';
        textarea.parentNode.insertBefore(wrapper, textarea);
        textarea.style.display = 'none';

        const editorDiv = document.createElement('div');
        wrapper.appendChild(editorDiv);

        const quill = new Quill(editorDiv, {
            theme: 'snow',
            placeholder: textarea.placeholder || 'Write here...',
            modules: { toolbar }
        });

        if (textarea.value.trim()) {
            quill.root.innerHTML = textarea.value;
        }

        textarea._quillInstance = quill;

        quill.on('text-change', function() {
            const html = quill.root.innerHTML;
            textarea.value = (html === '<p><br></p>') ? '' : html;
        });

        const form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                const html = quill.root.innerHTML;
                textarea.value = (html === '<p><br></p>') ? '' : html;
            }, true);
        }
    });
};

/**
 * Convert Quill v2 list HTML to standard <ul>/<ol> for proper browser rendering.
 */
window.renderQuillContent = function(container) {
    const root = container || document;
    root.querySelectorAll('.rich-text').forEach(function(el) {
        el.querySelectorAll('ol').forEach(function(ol) {
            const items = Array.from(ol.children);
            if (!items.some(li => li.hasAttribute('data-list'))) return;

            const fragment = document.createDocumentFragment();
            let currentList = null;
            let currentType = null;

            items.forEach(function(li) {
                const type = li.getAttribute('data-list');
                if (!type) return;

                li.querySelectorAll('.ql-ui').forEach(ui => ui.remove());

                if (type !== currentType) {
                    currentList = document.createElement(type === 'bullet' ? 'ul' : 'ol');
                    fragment.appendChild(currentList);
                    currentType = type;
                }

                const newLi = document.createElement('li');
                newLi.innerHTML = li.innerHTML;
                currentList.appendChild(newLi);
            });

            ol.parentNode.replaceChild(fragment, ol);
        });
    });
};

window.setQuillContent = function(textareaId, html) {
    const el = document.getElementById(textareaId);
    if (!el) return;
    if (el._quillInstance) {
        el._quillInstance.root.innerHTML = html || '';
        el.value = html || '';
    } else {
        el.value = html || '';
    }
};

document.addEventListener('DOMContentLoaded', function() {
    window.initQuillEditors();
    window.renderQuillContent();
});
