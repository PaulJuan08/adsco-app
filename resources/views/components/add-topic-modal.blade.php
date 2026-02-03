{{-- resources/views/components/add-topic-modal.blade.php --}}
<style>
    /* Modal Styles - Keep only the unique modal styles here */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .modal-overlay.active {
        display: flex;
    }
    
    .modal-container {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 600px;
        max-height: 80vh;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        animation: modalSlideIn 0.3s ease;
    }
    
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .modal-close {
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 6px;
    }
    
    .modal-close:hover {
        background: #f3f4f6;
    }
    
    .modal-body {
        padding: 1.5rem;
        max-height: calc(80vh - 120px);
        overflow-y: auto;
    }
    
    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }
    
    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    
    .btn-primary {
        background: #4f46e5;
        color: white;
    }
    
    .btn-primary:hover {
        background: #4338ca;
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }
    
    .btn-secondary:hover {
        background: #e5e7eb;
    }
    
    /* Topic List in Modal */
    .topics-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .topic-item {
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .topic-item:hover {
        border-color: #4f46e5;
        background: #f8fafc;
    }
    
    .topic-item.selected {
        border-color: #4f46e5;
        background: #f0f9ff;
        border-width: 2px;
    }
    
    .topic-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }
    
    .topic-item-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 1rem;
    }
    
    .topic-item-description {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    
    .add-btn {
        padding: 0.25rem 0.75rem;
        background: #10b981;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        opacity: 0;
        transition: all 0.2s;
    }
    
    .topic-item:hover .add-btn,
    .topic-item.selected .add-btn {
        opacity: 1;
    }
    
    .add-btn:hover {
        background: #059669;
    }
    
    .no-topics {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
    }
    
    .no-topics i {
        font-size: 2rem;
        color: #d1d5db;
        margin-bottom: 0.75rem;
    }
    
    /* Loading State */
    .loading {
        text-align: center;
        padding: 2rem;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #e5e7eb;
        border-top-color: #4f46e5;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

{{-- Modal HTML Structure --}}
<div class="modal-overlay" id="addTopicModal">
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-title">Add Topics to Course</div>
            <button class="modal-close" onclick="closeAddTopicModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search available topics..." id="modalTopicSearch" onkeyup="searchTopics()">
            </div>
            
            <div id="availableTopicsList" class="topics-list">
                <div class="loading">
                    <div class="spinner"></div>
                    <div>Loading topics...</div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeAddTopicModal()">Cancel</button>
            <button class="btn btn-primary" onclick="addSelectedTopics()">Add Selected Topics</button>
        </div>
    </div>
</div>

<script>
    // Modal-specific JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize search functionality
        const searchInput = document.getElementById('modalTopicSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', searchTopics);
        }
        
        // Close modal when clicking outside
        const modal = document.getElementById('addTopicModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeAddTopicModal();
                }
            });
        }
    });
    
    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.getElementById('addTopicModal').classList.contains('active')) {
            closeAddTopicModal();
        }
    });
</script>