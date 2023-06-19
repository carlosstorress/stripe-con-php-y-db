<!DOCTYPE html>
<html lang="en-US">
<head>
<title>Payment Cancel</title>
<meta charset="utf-8">

<!-- Stylesheet file -->
<link href="css/style.css" rel="stylesheet">
<style>
.container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
}

.status {
  background-color: #f0f0f0;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  text-align: center;
}

.status h1 {
  font-family: Arial, sans-serif;
  font-size: 28px;
  margin-bottom: 20px;
}

.status a {
  font-family: Arial, sans-serif;
  font-size: 18px;
  text-decoration: none;
  color: #333;
  padding: 10px 20px;
  border: 2px solid #333;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}

.status a:hover {
  background-color: #333;
  color: #fff;
}
</style>
</head>
<body>
<div class="container">
    <div class="status">
        <h1 class="error">Su transacción ha sido cancelada!</h1>
        <a href="index.php" class="btn-link">Regresar a la página del producto</a>
    </div>
</div>
</body>
</html>


<!-- <!DOCTYPE html>
<html lang="en-US">
<head>
<title>Payment Cancel</title>
<meta charset="utf-8">

<link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="status">
        <h1 class="error">Su transaccion ha sido cancelada!</h1>
    </div>
    <a href="index.php" class="btn-link">Regresar a la pagina del producto</a>
</div>
</body>
</html> -->