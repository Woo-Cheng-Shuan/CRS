<!DOCTYPE html>
<html lang="en">
<head>
  <title>Course Registration</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
.footer {
   position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   background-color: rebeccapurple;
   color: white;
   text-align: center;
}

.navbar-nav .nav-link:hover {
  font-weight: bold;
}
</style>

</head>
<body>

<nav class="navbar navbar-expand-lg bg-light" data-bs-theme="light">
  <div class="container-fluid">
    <a class="navbar-brand" href="advisor.php">CRS Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor03" aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor03">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="advisor.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="modifycourselist.php">View / Modify course list</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="addnewcourse.php">Add new course</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="courseapproval.php">To approve
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="courselist.php">Approved list</a>
        </li>
      </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>
      </ul>
    </div>
  </div>
</nav>
