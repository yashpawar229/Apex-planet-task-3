<?php
// Step 1: Connect to the database
$conn = new mysqli("localhost", "root", "", "blog_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Setup pagination variables
$limit = 5; // Posts per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Step 3: Handle search input
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchSafe = $conn->real_escape_string($search);

// Step 4: Count total posts for pagination
$countSQL = "SELECT COUNT(*) FROM posts WHERE title LIKE '%$searchSafe%' OR content LIKE '%$searchSafe%'";
$totalPosts = $conn->query($countSQL)->fetch_row()[0];
$totalPages = ceil($totalPosts / $limit);

// Step 5: Fetch posts for current page
$sql = "SELECT * FROM posts 
        WHERE title LIKE '%$searchSafe%' OR content LIKE '%$searchSafe%' 
        ORDER BY created_at DESC 
        LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search & Pagination</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">My Blog</a>
  </div>
</nav>

<!-- Main Container -->
<div class="container">
  <h2 class="mb-4 text-center">📚 Blog Posts</h2>

  <!-- Search Form -->
  <form method="GET" class="mb-4">
    <div class="input-group">
      <input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </form>

  <!-- Posts List -->
  <?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
          <p class="card-text"><?= nl2br(htmlspecialchars(substr($row['content'], 0, 200))) ?>...</p>
          <small class="text-muted">Posted on <?= $row['created_at'] ?></small>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-muted">No posts found.</p>
  <?php endif; ?>

  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

</body>
</html>
