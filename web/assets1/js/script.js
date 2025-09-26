
	 // btn
    document.getElementById('plotDropdown').addEventListener('click', () => {
      document.getElementById('plotList').classList.toggle('show');
    });
    document.querySelectorAll('.toggle-switch button').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.toggle-switch button').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
      });
    });
    document.querySelectorAll('.location-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.location-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
      });
    });