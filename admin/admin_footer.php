<?php
// admin/admin_footer.php - Admin Dashboard Footer
?>
        </main>
    </div>
</div>

<script>
// Confirm dialog helper for deletion
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function(e) {
        if (!confirm(this.getAttribute('data-confirm') || 'Are you sure you want to proceed?')) {
            e.preventDefault();
        }
    });
});
</script>
</body>
</html>
