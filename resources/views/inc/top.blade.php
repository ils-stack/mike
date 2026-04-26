<!-- Topbar -->
<div class="d-flex justify-content-between align-items-center p-3" style="background-color: #1b3556; color: white;">
  <div>
    <img src="/assets/img/koin_5.png" alt="Logo" class="logo me-2" style="height: 40px;">
    <strong>Demo CRM</strong>
  </div>
  <div>
    <a href="#" class="text-white me-3">Brochure <i class="fas fa-circle text-warning"></i></a>
    <span class="me-2">Welcome {{ Auth::user()->name ?? 'User' }}</span>
    <a href="" class="text-warning text-decoration-none"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      Logout
    </a>
    <form id="logout-form" action="" method="POST" class="d-none">
      @csrf
    </form>
  </div>
</div>
