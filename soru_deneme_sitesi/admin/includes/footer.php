    </div> <!-- End Content -->
</div> <!-- End Wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Scripts -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Sidebar Toggle Logic
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            if (sidebar.style.marginLeft === '-260px') {
                sidebar.style.marginLeft = '0';
            } else {
                sidebar.style.marginLeft = '-260px';
            }
        });
    });
</script>
</body>
</html>
