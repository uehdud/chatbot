</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<script>
    const sidebar = document.getElementById('sidebar');
    const toggleSidebar = document.getElementById('toggleSidebar');

    toggleSidebar.addEventListener('click', () => {
        sidebar.classList.toggle('show');
    });

    // Menutup sidebar saat area di luar sidebar diklik pada perangkat kecil
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 768 && !sidebar.contains(e.target) && !toggleSidebar.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });
</script>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#chat-table')) {
            $('#chat-table').DataTable().destroy(); // Hancurkan DataTables sebelumnya
        }

        $('#chat-table').DataTable({
            order: [] // Menonaktifkan pengurutan default
        });
    });
</script>

</body>

</html>