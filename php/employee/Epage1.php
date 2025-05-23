<?php
include '../import/database.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ToMart</title>
        <link rel="icon" href="../../style/ToMart_Logo.png" type="image/x-icon">
        <link rel="stylesheet" href="../../style/index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="../../script/Eindex.js" defer></script>
    </head>
    <body>
        <div class="body_container">
            <div class="side-bar"><!--Side bar for tabs-->
                <div class="logo-container">
                    <div class="logo-icon"></div>
                    <div class="logo-label">ToMart</div> 
                </div>
                <div class="username-container">
                        Hello! {Username}
                </div>
                <div class="tab-container">
                    <div class="tab active" data-target="items-content">
                        <div class="tab-icon">
                            <i class="fas fa-th-large"></i> 
                        </div>
                        <div class="tab-label">Inventory</div>
                    </div>
                    <div class="tab" data-target="discounts-content">
                        <div class="tab-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="tab-label">Cashier</div>
                    </div>
                    <div class="tab" data-target="orders-content">
                        <div class="tab-icon"><i class="fas fa-shopping-cart"></i></div>
                        <div class="tab-label">My Orders</div>
                    </div>
                </div>
                <div class="side-bar"></div>
            </div>
            <div class="center-bar"><!--Main bar for for stuffs-->
                <div class="content-container active" id="items-content"><!--Items content-->
                    <div class="center-bar-header">
                        <div class="category-toggle active" onclick="toggleCategory()">
                            <i class="fas fa-bars"></i>
                        </div>
                        <div class="search-bar-container">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search Product here...">
                        </div>
                        <div class="cart_toggle" onclick="toggleCart()">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                    <div class="center-bar-category-container active">
                        <div class="category-items-labels">
                            <div class="category active">ALL<br><small>99 Items</small></div>
                            <div class="category">Food & Snacks<br><small>30 Items</small></div>
                        </div>
                    </div>
                    <div class="center-bar-body-main-container">
                        <div class="item-card"><!--Item card can be multiplied-->
                            <div class="item-image"></div><!--Item image-->
                            <div class="item-details"><!--Items Details and action here-->
                                <div class="item-name">Item Name</div>
                                <div class="item-price">₱99.99</div>
                                <div class="div_button-container">
                                    <button class="add-to-order">Add to Order</button>
                                </div>
                            </div>
                        </div>
                        <script>
                            const centerBarBodyMainContainer = document.querySelector('.center-bar-body-main-container');
                            for (let i = 0; i < 10; i++) {
                                const itemCard = document.createElement('div');
                                itemCard.className = 'item-card';
                                centerBarBodyMainContainer.appendChild(itemCard);
                            }
                        </script>
                    </div>
                </div>
                <div class="content-container" id="discounts-content"><!--Discounts content-->
                    discount
                </div>
                <div class="content-container" id="orders-content"><!--My Orders content-->
                    my orders
                </div>
            </div>
            <div class="cart-bar active"><!--Side bar for Cart-->
                <div class="add-cart-container">
                    <div class="cart-top">tops</div>
                    <div class="cart-down">
                        <div class="total_container">
                            prices
                        </div>
                        <div class="payment_container">
                            <div class="box-toggle" onclick="togglePayment()">
                                <div class="cash-box active">
                                    Cash
                                </div>
                                <div class="ewallet-box">
                                    e-Wallet
                                </div>
                            </div>
                        </div>
                        <div class="order_container">
                            <button type="submit" class="order-button">Place Order</button>
                        </div>
                    </div>
                </div>
            </div> 
        </div> 
        <script>
            // Save active tab to localStorage
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const target = tab.getAttribute('data-target');
                    localStorage.setItem('activeTab', target);
                });
            });

            // Restore active tab on page load
            document.addEventListener('DOMContentLoaded', () => {
                const activeTab = localStorage.getItem('activeTab') || 'items-content';
                tabs.forEach(tab => {
                    const target = tab.getAttribute('data-target');
                    const content = document.getElementById(target);
                    if (target === activeTab) {
                        tab.classList.add('active');
                        content.classList.add('active');
                    } else {
                        tab.classList.remove('active');
                        content.classList.remove('active');
                    }
                });
            });
        </script>
    </body>
</html>