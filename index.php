<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Browser Fingerprint Study</title>
  <style>
    body { font-family: sans-serif; max-width: 480px; margin: 3rem auto; padding: 0 1rem; }
    label, input[type=text] { display: block; width: 100%; margin-bottom: 1rem; }
    input[type=text] { padding: .5rem; box-sizing: border-box; }
    button { padding: .6rem 1.2rem; }
    .msg { padding: .75rem; margin-bottom: 1rem; border-radius: 4px; }
    .ok { background: #e6f4ea; }
    .err { background: #fce8e6; }
  </style>
</head>
<body>
  <h1>Browser Fingerprint Study</h1>

  <?php if (isset($_GET['status']) && $_GET['status'] === 'ok'): ?>
    <div class="msg ok">Thank you. Your submission was recorded.</div>
  <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
    <div class="msg err">Something went wrong. Please try again.</div>
  <?php endif; ?>

  <form method="POST" action="submit.php" id="form">
    <label for="num_id">number identifier</label>
    <input type="text" id="num_id" name="num_id" maxlength="255" required>

    <input type="hidden" id="visitor_id" name="visitor_id">
    <input type="hidden" id="components" name="components">

    <label>
      <input type="checkbox" name="consent" value="1" required>
      I consent to my browser fingerprint being collected and understand the risks listed in the 
            <a href="consent.html" target="_blank" rel="noopener">consent terms</a>, 
    </label>

    <button type="submit" id="submit_btn" disabled>Submit</button>
    <p id="status">Calculating browser fingerprint (make sure ad blockers are turned off)…</p>
  </form>

  <script type="module">
    // Change the version here (v4 or v5) once you've decided.
    import FingerprintJS from 'https://openfpcdn.io/fingerprintjs/v4';

    const status = document.getElementById('status');
    const btn = document.getElementById('submit_btn');

    try {
      const fp = await FingerprintJS.load();
      const result = await fp.get();
      document.getElementById('visitor_id').value = result.visitorId;
      document.getElementById('components').value = JSON.stringify(result.components);
      status.textContent = 'Fingerprint ready.';
      btn.disabled = false;
    } catch (e) {
      status.textContent = 'Could not calculate fingerprint. Please try a different browser.';
    }
  </script>
</body>
</html>