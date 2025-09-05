// Sidebar toggle for mobile
const toggleBtn = document.querySelector('.toggle-btn');
const sidebar = document.querySelector('.sidebar');

if (toggleBtn && sidebar) {
  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('active');
    if (sidebar.classList.contains('active')) {
      sidebar.style.left = "0";
    } else {
      sidebar.style.left = "-250px";
    }
  });
}
