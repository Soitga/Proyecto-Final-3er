<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dining Room Manager</title>
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css" >
    <link rel="stylesheet" href="../css/DiningRoMCSS/homeDiningRoom.css">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
</head>
<body>
    <aside class="sidebar">
        <a href="homeDiningRoom.php" class="logo">Foody</a>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="homeDiningRoom.php"><i class="fas fa-home"></i>Home</a></li>
                <li><a href="viewOrderEmplo.php"><i class="fas fa-users"></i>Employees Orders</a></li>
                <li><a href="createDish.php"><i class="fas fa-utensils"></i>Create Dish</a></li>
                <li><a href="createmenu.php"><i class="fas fa-book"></i>Create Menu</a></li>
                <li><a href="inventoryIngredients.php"><i class="fas fa-warehouse"></i>Inventory</a></li>
                <li><a href="orderSupplier.php"><i class="fas fa-truck"></i>Order Supplier</a></li>
                <li><a href="createSupplier.php"><i class="fas fa-store"></i>Register Supplier</a></li>
                <li><a href="createReport.php"><i class="fas fa-chart-bar"></i>Create Reports</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i>View Reports</a></li>
                <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i>Log off</a></li>
            </ul>
        </nav>
    </aside>
    
    <button class="hamburger-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <main class="main-content">
        <div class="container">
            <h1 class="text-center">Welcome Dining Room Manager!</h1>
            <div class="row g-4">
            <div class="row g-4">
    <!-- Employees Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-users"></i>
                <h5 class="card-title">Employee Orders</h5>
                <p class="card-text">Manage and view employee orders.</p>
                <button><a href="viewOrderEmplo.php">View Orders</a></button>
            </div>
        </div>
    </div>

    <!-- Create Dish Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-utensils"></i>
                <h5 class="card-title">Dishes</h5>
                <p class="card-text">Manage your dishes.</p>
                <button><a href="createDish.php">Create Dish</a></button>
                <button><a href="viewDish.php">View Dishes</a></button>
            </div>
        </div>
    </div>

    <!-- Menu Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-book"></i>
                <h5 class="card-title">Menus</h5>
                <p class="card-text">Manage your dining room menus.</p>
                <button><a href="createmenu.php">Create Menu</a></button>
                <button><a href="viewMenu.php">View Menus</a></button>
            </div>
        </div>
    </div>

    <!-- Inventory Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-warehouse"></i>
                <h5 class="card-title">Inventory</h5>
                <p class="card-text">Manage your ingredients inventory.</p>
                <button><a href="inventoryIngredients.php">View Inventory</a></button>
            </div>
        </div>
    </div>

    <!-- Supplier Orders Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-truck"></i>
                <h5 class="card-title">Supplier Orders</h5>
                <p class="card-text">Manage orders from suppliers.</p>
                <button><a href="orderSupplier.php">Create Order</a></button>
                <button><a href="viewPurchaseOrder.php">View Orders</a></button>
            </div>
        </div>
    </div>

    <!-- Suppliers Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-store"></i>
                <h5 class="card-title">Suppliers</h5>
                <p class="card-text">Manage your suppliers.</p>
                <button><a href="createSupplier.php">Register Supplier</a></button>
                <button><a href="viewSuppliers.php">View Suppliers</a></button>
            </div>
        </div>
    </div>

    <!-- Reports Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-chart-bar"></i>
                <h5 class="card-title">Reports</h5>
                <p class="card-text">Generate and view reports.</p>
                <button><a href="createReport.php">Create Report</a></button>
                <button><a href="reports.php">View Reports</a></button>
            </div>
        </div>
    </div>


    <!-- Log off Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <i class="fas fa-sign-out-alt"></i>
                <h5 class="card-title">Log off</h5>
                <p class="card-text">Safely exit the system.</p>
                <button><a href="../index.php">Log off</a></button>
            </div>
        </div>
    </div>
</div>
</main>
        <script>
        
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const hamburgerBtn = document.querySelector('.hamburger-btn');

            sidebar.classList.toggle('open');
            hamburgerBtn.classList.toggle('active');
        }
    </script>
  
   <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <script src="/Project-Foody/Foody/javascript/employeeManagement.js"></script>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const hamburgerBtn = document.querySelector('.hamburger-btn');

            sidebar.classList.toggle('open');
            hamburgerBtn.classList.toggle('active'); 
        }
    </script>
  
    
</body>
</html>


