<?php
// QUERY: Get all users
require_once __DIR__ . '/../../src/Database.php';

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<?php if (isset($_GET['success']) && $_GET['success'] == 'user_created'): ?>
    <div
        style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
        <i class="ri-checkbox-circle-fill"></i> User baru berhasil ditambahkan!
    </div>
<?php endif; ?>

<!-- Main Card Container -->
<div
    style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; margin-bottom: 30px;">

    <!-- Card Header (Blue) -->
    <div
        style="padding: 20px 24px; background: var(--primary); border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: white;">Kelola User</h2>
        <button onclick="document.getElementById('addUserModal').style.display='flex'" class="btn"
            style="font-size: 0.9rem; background: white; color: var(--primary); border: none; font-weight: 600;">
            <i class="ri-user-add-line" style="margin-right: 6px;"></i> Tambah User
        </button>
    </div>

    <!-- Table Container -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead style="background: #f8fafc; color: #475569; border-bottom: 1px solid #e2e8f0;">
                <tr>
                    <th style="padding: 16px 24px; text-align: left; font-size: 0.9rem; font-weight: 600;">
                        User
                    </th>
                    <th style="padding: 16px 24px; text-align: left; font-size: 0.9rem; font-weight: 600;">
                        Role
                    </th>
                    <th style="padding: 16px 24px; text-align: left; font-size: 0.9rem; font-weight: 600;">
                        Dibuat
                    </th>
                    <th style="padding: 16px 24px; text-align: right; font-size: 0.9rem; font-weight: 600;">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody style="background: white;">
                <?php foreach ($users as $u): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s ease;"
                        onmouseover="this.style.background='#f8fafc'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';"
                        onmouseout="this.style.background='white'; this.style.transform='none'; this.style.boxShadow='none';">

                        <!-- User Info (Avatar + Name + Username) -->
                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 40px; height: 40px; background: #e0e7ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #4338ca; font-weight: 700; font-size: 1rem; margin-right: 14px; border: 2px solid white; box-shadow: 0 0 0 1px #c7d2fe; transition: transform 0.2s ease;"
                                    onmouseover="this.style.transform='scale(1.1)'"
                                    onmouseout="this.style.transform='scale(1)'">
                                    <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">
                                        <?php echo htmlspecialchars($u['name']); ?>
                                    </div>
                                    <div style="font-size: 0.85rem; color: #64748b;">
                                        @<?php echo htmlspecialchars($u['username']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Role -->
                        <td style="padding: 16px 24px;">
                            <?php
                            $roleColors = [
                                'admin' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#dbeafe'],
                                'staff' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#dcfce7'],
                                'mahasiswa' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#ffedd5'],
                                'user' => ['bg' => '#f1f5f9', 'text' => '#64748b', 'border' => '#e2e8f0']
                            ];
                            $rc = $roleColors[$u['role']] ?? $roleColors['user'];
                            ?>
                            <span
                                style="display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: <?php echo $rc['bg']; ?>; color: <?php echo $rc['text']; ?>; border: 1px solid <?php echo $rc['border']; ?>; text-transform: uppercase; letter-spacing: 0.5px;">
                                <span
                                    style="width: 6px; height: 6px; background: currentColor; border-radius: 50%; margin-right: 6px;"></span>
                                <?php echo htmlspecialchars($u['role'] ?: '-'); ?>
                            </span>
                        </td>

                        <!-- Tanggal Dibuat -->
                        <td style="padding: 16px 24px; color: #64748b; font-size: 0.85rem;">
                            <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                        </td>

                        <!-- Aksi -->
                        <td style="padding: 16px 24px; text-align: right;">
                            <button
                                onclick="openEditModal('<?php echo $u['id']; ?>', '<?php echo htmlspecialchars($u['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($u['username'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($u['email'] ?? '', ENT_QUOTES); ?>', '<?php echo $u['role']; ?>')"
                                style="width: 36px; height: 36px; border-radius: 50%; border: none; background: #eff6ff; color: var(--primary); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                                onmouseover="this.style.background='#dbeafe'; this.style.transform='scale(1.1)'"
                                onmouseout="this.style.background='#eff6ff'; this.style.transform='scale(1)'"
                                title="Edit User">
                                <i class="ri-pencil-fill"></i>
                            </button>

                            <?php if (isset($_SESSION['user']) && $u['id'] != $_SESSION['user']['id']): ?>
                                <form action="?page=delete_user" method="POST" style="display:inline;"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? \nPERINGATAN: Semua tiket dan data terkait user ini akan DIHAPUS PERMANEN!');">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit"
                                        style="width: 36px; height: 36px; border-radius: 50%; border: none; background: #fef2f2; color: #ef4444; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; margin-left: 8px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                                        onmouseover="this.style.background='#fee2e2'; this.style.transform='scale(1.1)'"
                                        onmouseout="this.style.background='#fef2f2'; this.style.transform='scale(1)'"
                                        title="Hapus User">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah User -->
<div id="addUserModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
    <div
        style="background: white; border-radius: 16px; width: 100%; max-width: 420px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: slideIn 0.2s ease-out;">
        <!-- Header -->
        <div
            style="padding: 20px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">Tambah User Baru</h3>
            <button onclick="document.getElementById('addUserModal').style.display='none'"
                style="border: none; background: transparent; color: #94a3b8; cursor: pointer; font-size: 1.25rem;">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <form action="?page=create_user" method="POST" style="padding: 24px;">
            <div class="form-group mb-4">
                <label
                    style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Nama
                    Lengkap</label>
                <input type="text" name="name" class="form-control" required placeholder="Contoh: Siti Aminah"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;">
            </div>

            <div class="form-group mb-4">
                <label
                    style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Username
                    (Login)</label>
                <input type="text" name="username" class="form-control" required placeholder="Contoh: sitia"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;">
            </div>

            <div class="form-group mb-4">
                <label
                    style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Role</label>
                <div style="position: relative;">
                    <select name="role" class="form-control"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; appearance: none; background-color: white;">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Administrator</option>
                    </select>
                    <i class="ri-arrow-down-s-line"
                        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none;"></i>
                </div>
            </div>

            <div class="form-group mb-4">
                <label
                    style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;">
            </div>

            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" class="btn"
                    style="flex: 1; padding: 12px; background: white; border: 1px solid #e2e8f0; color: #64748b; font-weight: 600; border-radius: 8px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" class="btn"
                    style="flex: 1; padding: 12px; background: var(--primary); color: white; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="editUserModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
    <div
        style="background: white; border-radius: 16px; width: 100%; max-width: 420px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: slideIn 0.2s ease-out;">
        <!-- Header -->
        <div
            style="padding: 20px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">Edit User</h3>
            <button onclick="document.getElementById('editUserModal').style.display='none'"
                style="border: none; background: transparent; color: #94a3b8; cursor: pointer; font-size: 1.25rem;">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <form action="?page=update_user" method="POST" style="padding: 24px;">
            <input type="hidden" name="user_id" id="edit_user_id">

            <div class="form-group mb-4">
                <label
                    style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Nama
                    Lengkap</label>
                <input type="text" name="name" id="edit_name" class="form-control" required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;">
            </div>

            <div class="form-group mb-4">
                <label
                    style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Username</label>
                <input type="text" name="username" id="edit_username" class="form-control" required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;">
            </div>

            <div class="form-group mb-4">
                <label
                    style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155; font-size: 0.9rem;">Role</label>
                <div style="position: relative;">
                    <select name="role" id="edit_role" class="form-control"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; appearance: none; background-color: white;">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Administrator</option>
                    </select>
                    <i class="ri-arrow-down-s-line"
                        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none;"></i>
                </div>
            </div>

            <!-- Separator -->
            <hr style="border: 0; border-top: 1px dashed #e2e8f0; margin: 24px 0;">

            <div class="form-group mb-4">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #64748b; font-size: 0.9rem;">
                    <i class="ri-lock-password-line" style="margin-right: 6px;"></i> Ubah Password
                </label>
                <input type="password" name="password" class="form-control"
                    placeholder="Input password baru jika ingin mengubah"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; outline: none; transition: all 0.2s;">
                <small style="color: #94a3b8; font-size: 0.8rem; margin-top: 6px; display: block;">Kosongkan jika tidak
                    ingin mengubah.</small>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="button" onclick="document.getElementById('editUserModal').style.display='none'"
                    class="btn"
                    style="flex: 1; padding: 12px; background: white; border: 1px solid #e2e8f0; color: #64748b; font-weight: 600; border-radius: 8px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" class="btn"
                    style="flex: 1; padding: 12px; background: var(--primary); color: white; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">
                    Update Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name, username, email, role) {
        document.getElementById('editUserModal').style.display = 'flex';
        document.getElementById('edit_user_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_role').value = role;
    }
</script>