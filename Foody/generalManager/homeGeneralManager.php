<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

$queryUsers = "SELECT COUNT(*) as total_users FROM users";
$resultUsers = mysqli_query($conn, $queryUsers);
$rowUsers = mysqli_fetch_assoc($resultUsers);
$totalUsers = $rowUsers['total_users'];

$queryFactories = "SELECT COUNT(*) as total_factories FROM factory";
$resultFactories = mysqli_query($conn, $queryFactories);
$rowFactories = mysqli_fetch_assoc($resultFactories);
$totalFactories = $rowFactories['total_factories'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home General Manager</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/generalManagerCss/homeGeneralMa.css">
        
</head>
<body>
    <button class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="#" class="logo">Foody</a>
        </div>
        <nav class="sidebar-nav">
            <ul>
            <header>
    <div class="menu">
        <a href="homeGeneralManager.php" class="logo">Foody</a>
        <nav class="navbar">
            <ul>
                <li><a href="../index.php">Log off</a></li>
            </ul>
        </nav>
    </div>
</header>

<button class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<aside class="sidebar">
    <div class="sidebar-header">
        <a href="homeGeneralManager.php" class="logo">Foody</a>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="registerEmplo.php">
                <i class="fas fa-user-plus"></i>
                <span>Register Users</span>
            </a></li>
            <li><a href="listUsers.php">
                <i class="fas fa-users"></i>
                <span>See Created Users</span>
            </a></li>
            <li><a href="registerfactory.php">
                <i class="fas fa-industry"></i>
                <span>Register Factory</span>
            </a></li>
            <li><a href="listFactory.php">
                <i class="fas fa-building"></i>
                <span>See Factories</span>
            </a></li>
            <li><a href="registerDining.php">
                <i class="fas fa-utensils"></i>
                <span>Register Dining Room</span>
            </a></li>
            <li><a href="listDiningRooms.php">
                <i class="fas fa-utensils"></i>
                <span>See Dining Room</span>
            </a></li>
            <li><a href="../index.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Log off</span>
            </a></li>
        </ul>
    </nav>
</aside>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <div class="container-fluid">
            <h1 class="text-center mb-4">Welcome General Manager!</h1>

            <div class="profile-card mb-4">
                <div class="profile-icon">
                    <i class="fas fa-user fa-3x text-brown"></i>
                </div>
                <h2 class="text-center mb-4">General Manager Profile</h2>
                
                <div class="quick-actions">
                    <div class="row g-4">
                        <div class="col-md-4">
                            
                            <div class="card h-100">
                            <a href="registerEmplo.php">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-plus fa-2x text-brown mb-3"></i>
                                    <h5 class="card-title">Register Users</h5>
                                    <p class="card-text">Add new users to the system</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <a href="registerfactory.php">
                                <div class="card-body text-center">
                                    <i class="fas fa-industry fa-2x text-brown mb-3"></i>
                                    <h5 class="card-title">Register Factory</h5>
                                    <p class="card-text">Add new factories</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <a href="listFactory.php">
                                <div class="card-body text-center">
                                    <i class="fas fa-building fa-2x mb-3"></i>
                                    <h5 class="card-title">View Factories</h5>
                                    <p class="card-text">Manage existing factories</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row justify-content-center"> 
                <div class="col-md-6 col-lg-3"> 
                    <div class="card text-center"> 
                        <div class="card-body"> 
                            <i class="fas fa-users fa-2x mb-3"></i>
                             <h5 class="card-title">Total Users</h5>
                              <p class="card-text"><?php echo $totalUsers; ?></p> </div>
                             </div> </div> <div class="col-md-6 col-lg-3"> 
                                <div class="card text-center">
                                     <div class="card-body"> 
                                        <i class="fas fa-industry fa-2x mb-3"></i> 
                                        <h5 class="card-title">Active Factories</h5> 
                                        <p class="card-text"><?php echo $totalFactories; ?></p> 
                                    </div>
                                </div> 
                            </div> 
             </div> 
         </div> 
        </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>