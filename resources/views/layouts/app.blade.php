<script>
document.addEventListener('DOMContentLoaded', function () {
    window.addEventListener('open-new-tab', function (event) {
        window.open(event.detail.url, '_blank');
    });
});
</script>
