<?php
$firstName = explode(' ', $currentUser['name'])[0];
$avatar = !empty($currentUser['avatar_url']) ? $currentUser['avatar_url'] : "https://ui-avatars.com/api/?name=" . urlencode($currentUser['name']) . "&background=eff6ff&color=1d4ed8";
?>

<div class="user-wrapper">
    <div class="user-pill" onclick="toggleProfileMenu()">
        <img src="<?php echo $avatar; ?>" alt="Profile">
        <div class="user-text">
            <span class="user-name"><?php echo htmlspecialchars($firstName); ?></span>
        </div>
        <span class="user-role-badge"><?php echo $currentUser['role']; ?></span>
        <i class="ri-arrow-down-s-line"></i>
    </div>

    <!-- Dropdown Menu -->
    <div id="profile-menu">
        <div class="menu-header">
            <div class="menu-name"><?php echo htmlspecialchars($currentUser['name']); ?></div>
            <div class="menu-username"><?php echo htmlspecialchars($currentUser['username']); ?></div>
        </div>
    </div>
</div>