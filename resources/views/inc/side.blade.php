<ul class="nav flex-column gap-1">

  <!-- CORE / DASHBOARD -->
  <li class="nav-item">
    <a class="nav-link text-white d-flex align-items-center" href="/">
      <i class="fas fa-dashboard me-2"></i> Dashboard
    </a>
  </li>

  <!-- CLIENT & PERSONAL INFORMATION -->
  <li class="nav-item">
    <a class="nav-link text-white d-flex justify-content-between align-items-center"
       data-bs-toggle="collapse"
       href="#clientMenu"
       role="button">
      <span>
        <i class="fas fa-users me-2"></i>Personal Information
      </span>
      <i class="fas fa-chevron-down small"></i>
    </a>

    <ul class="collapse nav flex-column ms-3" id="clientMenu">
      <li class="nav-item">
        <a class="nav-link text-white" href="/family-details">
          Family Details
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" href="/notes-tasks">
          Notes & Tasks
        </a>
      </li>
    </ul>
  </li>

  <!-- FINANCIAL PLANNING & ANALYSIS -->
  <li class="nav-item">
    <a class="nav-link text-white d-flex justify-content-between align-items-center"
       data-bs-toggle="collapse"
       href="#financeMenu"
       role="button">
      <span>
        <i class="fas fa-chart-line me-2"></i> Financial Planning
      </span>
      <i class="fas fa-chevron-down small"></i>
    </a>

    <ul class="collapse nav flex-column ms-3" id="financeMenu">
      <li class="nav-item">
        <a class="nav-link text-white" href="/budget">
          Budget
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" href="/crm-assets">
          Assets
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" href="/short-assessments">
          Calculators
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" href="/short-assessments">
          Short Assessments
        </a>
      </li>
    </ul>
  </li>

  <!-- INSURANCE, POLICIES & COMPLIANCE -->
  <li class="nav-item">
    <a class="nav-link text-white d-flex justify-content-between align-items-center"
       data-bs-toggle="collapse"
       href="#insuranceMenu"
       role="button">
      <span>
        <i class="fas fa-file-shield me-2"></i> Compliance
      </span>
      <i class="fas fa-chevron-down small"></i>
    </a>

    <ul class="collapse nav flex-column ms-3" id="insuranceMenu">
      <li class="nav-item">
        <a class="nav-link text-white" href="/policies">
          Policy Details
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white" href="/compliance">
          Compliance
        </a>
      </li>
    </ul>
  </li>

  <!-- DOCUMENTS -->
  <li class="nav-item">
    <a class="nav-link text-white d-flex align-items-center" href="/documents">
      <span>
        <i class="fas fa-folder-open me-2"></i> Documents
      </span>
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link text-white d-flex align-items-center" href="/source">
      <span>
        <i class="fas fa-file-import me-2"></i> Source Admin
      </span>
    </a>
  </li>

  <!-- REPORTS -->
<li class="nav-item">
  <a class="nav-link text-white d-flex align-items-center" href="/reports">
    <span>
      <i class="fas fa-chart-bar me-2"></i> Reports
    </span>
  </a>
</li>

  <!-- LOGOUT -->
  <li class="nav-item mt-2">
    <a class="nav-link text-white" href="/logout">
      <i class="fas fa-sign-out-alt me-2"></i> Logout
    </a>
  </li>

</ul>
