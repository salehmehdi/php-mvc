<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Üye Girişi</title>
</head>
<body>
  <?php if (! empty($member)): ?>
    <p>Welcome <?php 
      echo htmlspecialchars($member['name'] ?? '', ENT_QUOTES, 'UTF-8')
         . ' '
         . htmlspecialchars($member['surname'] ?? '', ENT_QUOTES, 'UTF-8');
    ?></p>
  <?php else: ?>
    <p>Welcome, guest!</p>
  <?php endif; ?>
</body>
</html>
