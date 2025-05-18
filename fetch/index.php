<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>fetch</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/general_css.css">
    <script src="js/dark_mode.js" defer></script>
    <link rel="shortcut icon" type="image/x-icon" href="img/f.png" />
</head>

<body>
    <?php
        session_start();
        require_once 'config.php';

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        $userId = $_SESSION['user_id'];
        
        // Get user's budget
        $budget_sql = $conn->prepare("SELECT budget FROM user_budgets WHERE user_id = ?");
        $budget_sql->bind_param("i", $userId);
        $budget_sql->execute();
        $budget_result = $budget_sql->get_result();
        $user_budget = 0;
        
        if ($budget_result->num_rows > 0) {
            $budget_row = $budget_result->fetch_assoc();
            $user_budget = (float)$budget_row['budget'];
            $_SESSION['budget'] = $user_budget;
        }
        
        // Get all items and calculate total spent
        $sql = $conn->prepare("SELECT * FROM items WHERE user_id = ?");
        $sql->bind_param("i", $userId);
        $sql->execute();
        $result = $sql->get_result();
        
        $total_spent = 0;
        while ($row = $result->fetch_assoc()) {
            $total_spent += (float)$row['items_price'];
        }
        $remaining_budget = $user_budget - $total_spent;
    ?>
    
    <div class="wrapper">
    <header>
        <div class="header__container">
        <nav class="navMenu">
            <div class="headerLogo">
                <a class = "logo" href="index.php">
                    fetch
                </a>
            </div>
            <ul class="headerList">
                <a href="login.php" class="headerMenuL">Login</a>
               
                <li class="mainList themeSwitchButton">
                    <button id="theme-switch">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q14 0 27.5 1t26.5 3q-41 29-65.5 75.5T444-660q0 90 63 153t153 63q55 0 101-24.5t75-65.5q2 13 3 26.5t1 27.5q0 150-105 255T480-120Z"/></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480q0 83-58.5 141.5T480-280ZM200-440H40v-80h160v80Zm720 0H760v-80h160v80ZM440-760v-160h80v160h-80Zm0 720v-160h80v160h-80ZM256-650l-101-97 57-59 96 100-52 56Zm492 496-97-101 53-55 101 97-57 59Zm-98-550 97-101 59 57-100 96-56-52ZM154-212l101-97 55 53-97 101-59-57Z"/></svg>
                    </button>
                </li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>
        </div>
    </header>
    <main class="main">
        <div class="main__container">
            <div class="budget-section ">
                <h2 class="formTitle">Budget Tracker</h2>
                <form action="set_budget.php" method="post">
                    <input type="number" name="budget" placeholder="Enter budget (CHF)" step="0.01" min="0" value="<?php echo isset($user_budget) ? htmlspecialchars($user_budget) : ''; ?>" required>
                    <button type="submit" class="button">Update Budget</button>
                </form>
            <div class="budget-display">
        <p>Total Budget: CHF <span id="total-budget"><?php echo number_format($user_budget, 2); ?></span></p>
        <p>Total Spent: CHF <span id="total-spent"><?php echo number_format($total_spent, 2); ?></span></p>
        <p>Remaining Budget: CHF <span id="remaining-budget" class="<?php echo $remaining_budget < 0 ? 'text-danger' : ''; ?>">
            <?php echo number_format($remaining_budget, 2); ?>
        </span></p>
        <div class="budget-bar">
            <div class="budget-progress <?php 
                echo $remaining_budget < 0 ? 'budget-exceeded' : 
                    ($remaining_budget/$user_budget < 0.2 ? 'budget-danger' : 
                    ($remaining_budget/$user_budget < 0.5 ? 'budget-warning' : '')); 
            ?>" style="width: <?php 
                echo $user_budget > 0 ? min(100, max(0, ($remaining_budget/$user_budget)*100)) : 0; 
            ?>%"></div>
        </div>
    </div>
</div>

            <div class="addItem">
                <h2 class="formTitle">Add New Item</h2>
                <form action="add_item.php" method="post" enctype="multipart/form-data">
                    <input type="text" name="name" placeholder="Item Name" required>
                    <input type="number" name="price" placeholder="Price (CHF)" step="0.01" min="0" required>
                    <input type="file" name="image" accept="image/*" required>
                    <button type="submit" name="add" class="button">Add Item</button>
                </form>
            </div>

            <div class="itemsGrid">
                <?php 
                $result->data_seek(0); // Reset result pointer
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()): 
                        $imagePath = (!empty($row['items_picture_path']) && file_exists($row['items_picture_path'])) 
                            ? $row['items_picture_path'] 
                            : 'img/default.jpg';
                ?>
                <div class="item">
                    <div class="item-image-container">
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['items_name']); ?>">
                        <form action="delete_item.php" method="post" class="delete-form">
                            <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="delete-btn" title="Delete item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="--basic-black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"></path>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <p><?php echo htmlspecialchars($row['items_name']); ?></p>
                    <p>CHF <?php echo number_format($row['items_price'], 2); ?></p>
                </div>
                <?php endwhile; 
                } else { ?>
                <p class="no-items">No items found. Add your first item!</p>
                <?php } ?>
            </div>
        </div>
    </main>
    <footer class="footer">
        <div class="footer__container">
            
        </div>
    </footer>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    
    // Budget display updates
    const progressBar = document.querySelector('.budget-progress');
    const remainingBudgetEl = document.getElementById('remaining-budget');
    const totalBudget = <?php echo $user_budget; ?>;
    const remainingBudget = <?php echo $remaining_budget; ?>;
    
    if (totalBudget > 0) {
        const percentage = Math.min(100, (remainingBudget / totalBudget) * 100);
        
        // Update progress bar color
        progressBar.classList.remove('budget-warning', 'budget-danger', 'budget-exceeded');
        if (remainingBudget < 0) {
            progressBar.classList.add('budget-exceeded');
            remainingBudgetEl.classList.add('text-danger');
        } else if (percentage < 20) {
            progressBar.classList.add('budget-danger');
        } else if (percentage < 50) {
            progressBar.classList.add('budget-warning');
        }
    }
});
    </script>
</body>
</html>