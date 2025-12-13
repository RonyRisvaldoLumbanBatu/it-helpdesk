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

<div class="card-header"
    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Kelola User</h2>
    <button onclick="document.getElementById('addUserModal').style.display='flex'" class="btn btn-primary">
        <i class="ri-user-add-line" style="margin-right: 8px;"></i> Tambah User
    </button>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] == 'user_created'): ?>
    <div
        style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
        <i class="ri-checkbox-circle-fill"></i> User baru berhasil ditambahkan!
    </div>
<?php endif; ?>

<div style="background: white; border-radius: 8px; border: 1px solid var(--border); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8fafc; border-bottom: 1px solid var(--border);">
            <tr>
                <th style="padding: 12px 16px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">NAMA
                </th>
                <th style="padding: 12px 16px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">USERNAME
                </th>
                <th style="padding: 12px 16px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">ROLE
                </th>
                <th style="padding: 12px 16px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">DIBUAT
                </th>
                <th style="padding: 12px 16px; text-align: right; font-size: 0.85rem; color: var(--text-muted);">AKSI
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 12px 16px; font-size: 0.9rem;">
                        <strong><?php echo htmlspecialchars($u['name']); ?></strong>
                    </td>
                    <td style="padding: 12px 16px; font-size: 0.9rem;">
                        <?php echo htmlspecialchars($u['username']); ?>
                    </td>
                    <td style="padding: 12px 16px;">
                        <?php
                        $roleColors = [
                            'admin' => ['bg' => '#e0e7ff', 'text' => '#4f46e5'],
                            'staff' => ['bg' => '#f0fdf4', 'text' => '#166534'], // Green
                            'mahasiswa' => ['bg' => '#fff7ed', 'text' => '#c2410c'], // Orange
                            'user' => ['bg' => '#f1f5f9', 'text' => '#64748b'] // Grey (Default)
                        ];
                        $rc = $roleColors[$u['role']] ?? $roleColors['user'];
                        ?>
                        <span
                            style="background: <?php echo $rc['bg']; ?>; color: <?php echo $rc['text']; ?>; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">
                            <?php echo htmlspecialchars($u['role'] ?: '-'); ?>
                        </span>
                    </td>
                    <td style="padding: 12px 16px; font-size: 0.9rem; color: var(--text-muted);">
                        <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                    </td>
                    <td style="padding: 12px 16px; text-align: right; white-space: nowrap;">
                        <button
                            onclick="openEditModal('<?php echo $u['id']; ?>', '<?php echo htmlspecialchars($u['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($u['username'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($u['email'] ?? '', ENT_QUOTES); ?>', '<?php echo $u['role']; ?>')"
                            style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid #dbeafe; background: #eff6ff; color: var(--primary); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s;"
                            onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'"
                            title="Edit User">
                            <i class="ri-pencil-line"></i>
                        </button>

                        <?php if (isset($_SESSION['user']) && $u['id'] != $_SESSION['user']['id']): ?>
                            <form action="?page=delete_user" method="POST" style="display:inline;"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? \nPERINGATAN: Semua tiket dan data terkait user ini akan DIHAPUS PERMANEN!');">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <button type="submit"
                                    style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid #fee2e2; background: #fef2f2; color: #ef4444; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; margin-left: 6px; transition: all 0.2s;"
                                    onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'"
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

<!-- Modal Tambah User -->
<div id="addUserModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
    <div
        style="background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-bottom: 20px;">Tambah User Baru</h3>

        <form action="?page=create_user" method="POST">
            <div class="form-group mb-4">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required placeholder="Contoh: Siti Aminah">
            </div>

            <div class="form-group mb-4">
                <label>Username (Login)</label>
                <input type="text" name="username" class="form-control" required placeholder="Contoh: sitia">
            </div>

            <div class="form-group mb-4">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <div class="form-group mb-4">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" class="btn"
                    style="background: #f1f5f9; color: var(--text-muted);">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan User</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="editUserModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
    <div
        style="background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-bottom: 20px; color: var(--primary);">Edit User</h3>

        <form action="?page=update_user" method="POST">
            <input type="hidden" name="user_id" id="edit_user_id">

            <div class="form-group mb-4">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            <div class="form-group mb-4">
                <label>Username</label>
                <input type="text" name="username" id="edit_username" class="form-control" required>
            </div>

            <div class="form-group mb-4">
                <label>Role</label>
                <select name="role" id="edit_role" class="form-control">
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <div class="form-group mb-4"
                style="background: #fff7ed; padding: 10px; border-radius: 6px; border: 1px solid #ffedd5;">
                <label style="color: #9a3412;">Reset Password (Opsional)</label>
                <input type="password" name="password" class="form-control"
                    placeholder="Isi HANYA jika ingin ganti password">
                <small style="color: #9a3412; font-size: 0.75rem;">Biarkan kosong jika tidak ingin mengubah
                    password.</small>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('editUserModal').style.display='none'"
                    class="btn" style="background: #f1f5f9; color: var(--text-muted);">Batal</button>
                <button type="submit" class="btn btn-primary">Update Data</button>
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