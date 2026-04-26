<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vsure CRM Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  html, body {
    height: 100%;
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
  }

  body {
    position: relative;
    overflow: hidden;
    background-color: #343A40;
  }

  body::before {
    content: '';
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;

    height: 10vh; /* desktop default */

    background-image: url('{{ asset('assets/banners/crm.png') }}');
    background-color: #363637;
    background-repeat: no-repeat;
    background-position: bottom center;
    background-size: auto 100%;

    z-index: -1;
    pointer-events: none;
  }

  /* 🔽 MOBILE FIX */
  @media (max-width: 768px) {
    body::before {
      height: 14vh;               /* give it breathing room */
      background-size: contain;  /* NO CROPPING */
    }
  }


    .login-wrapper {
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 1rem;
    }
    .login-card {
      background: rgba(255, 255, 255, 0.9);
      padding: 2rem;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 0 12px rgba(0,0,0,0.3);
      border-radius: 0.5rem;
    }
    .vsure-crm-logo {
      background-color: #E34234;
      color: white;
      text-align: center;
      padding: 10px;
      font-size: 1.8rem;
      font-weight: bold;
    }
    .vsure-crm-logo span {
      color: #fff;
      font-size: 0.8rem;
      display: block;
      letter-spacing: 1px;
    }
    .form-label {
      font-weight: 600;
    }
    .btn-login {
      background-color: #ccc;
      color: #000;
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="login-wrapper">
  <div class="login-card rounded">
    <div class="vsure-crm-logo">
      Vsure CRM<br><span>ERP Solutions</span>
    </div>

    @if($errors->any())
        <p style="color:red;">{{ $errors->first() }}</p>
    @endif

    <h5 class="mt-4 mb-3">LOGIN</h5>
    <form method="POST" action="{{ url('/login') }}">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Email*</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password*</label>
        <div class="d-flex justify-content-between">
          <input type="password" class="form-control me-2" id="password" name="password" required>
          <!-- <small><a href="#" class="text-decoration-none">Forgot your password?</a></small> -->
        </div>
      </div>

      <!-- <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="keepLoggedIn">
        <label class="form-check-label" for="keepLoggedIn">
          Keep me logged in
        </label>
      </div> -->

      <button type="submit" class="btn btn-login w-100">LOGIN</button>
    </form>
  </div>
</div>

</body>
</html>
