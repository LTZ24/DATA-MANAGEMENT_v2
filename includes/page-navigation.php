<?php
/**
 * Page Navigation Component
 */
?>
<div class="page-navigation">
    <button type="button" class="nav-btn nav-refresh" title="Refresh Halaman">
        <i class="fas fa-sync-alt"></i>
        <span>Refresh</span>
    </button>
    <a href="<?php echo BASE_URL; ?>/index.php" class="nav-btn nav-home" title="Dashboard">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.querySelector('.nav-refresh');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            window.location.reload();
        });
    }
});
</script>

<style>
.page-navigation {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.nav-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 1px solid var(--border-color);
    background: white;
    color: var(--text-color);
    border-radius: 8px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    font-family: inherit;
}

.nav-btn:hover {
    background: var(--light-color);
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
}

.nav-btn i {
    font-size: 0.9rem;
}

.nav-refresh {
    border-color: #10b981;
    color: #10b981;
}

.nav-refresh:hover {
    background: #10b981;
    color: white;
}

.nav-refresh:hover i {
    animation: spin 0.6s linear;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.nav-home {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.nav-home:hover {
    background: var(--primary-color);
    color: white;
}

@media (max-width: 480px) {
    .page-navigation {
        gap: 8px;
    }
    
    .nav-btn {
        padding: 8px 12px;
        font-size: 0.85rem;
    }
    
    .nav-btn span {
        display: none;
    }
    
    .nav-btn i {
        font-size: 1rem;
    }
}
</style>
