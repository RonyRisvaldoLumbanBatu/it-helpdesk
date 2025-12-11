<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Ganti Password</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            padding: 8px;
            width: 100%;
            max-width: 300px;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h1>Form Pengajuan Ganti Password Email</h1>
    <form action="" method="POST">
        <div class="form-group">
            <label>Email Kantor</label>
            <input type="email" name="email" required placeholder="nama@perusahaan.com">
        </div>
        <div class="form-group">
            <label>Alasan Penggantian</label>
            <input type="text" name="reason" required placeholder="Lupa password / Expired">
        </div>
        <button type="submit">Ajukan Request</button>
    </form>
</body>

</html>