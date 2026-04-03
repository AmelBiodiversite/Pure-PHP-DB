<?php /* MARKETFLOW PRO — CONNEXION */ ?>
<div class="auth-wrap">
<div class="auth-card">
  <div class="auth-head">
    <div class="auth-logo">M</div>
    <h1 class="auth-title">Bon retour</h1>
    <p class="auth-sub">Connectez-vous pour continuer</p>
  </div>
  <?php if (isset($error)): ?>
  <div class="auth-err">
    <svg viewBox="0 0 16 16" fill="none" width="15" height="15"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.4"/><path d="M8 5v3.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><circle cx="8" cy="11" r=".6" fill="currentColor"/></svg>
    <?= e($error) ?>
  </div>
  <?php endif; ?>
  <form method="POST" action="/login" id="lf">
    <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
    <div class="af">
      <label class="al" for="email">Adresse email</label>
      <input type="email" id="email" name="email" class="ai" placeholder="vous@example.com"
        value="<?= isset($email) ? e($email) : '' ?>" required autofocus>
    </div>
    <div class="af">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
        <label class="al" for="password" style="margin:0">Mot de passe</label>
        <a href="/forgot-password" class="auth-fgt">Oublié ?</a>
      </div>
      <div style="position:relative">
        <input type="password" id="password" name="password" class="ai" style="padding-right:40px" placeholder="••••••••" required>
        <button type="button" class="auth-eye" onclick="tp()" title="Afficher/masquer">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>
    </div>
    <button type="submit" class="auth-btn" id="lsb">Se connecter</button>
  </form>
  <div class="auth-sep"><span>ou</span></div>
  <p class="auth-sw">Pas encore de compte ? <a href="/register">S'inscrire gratuitement →</a></p>
  <p class="auth-legal">En vous connectant, vous acceptez nos <a href="/terms">CGU</a> et notre <a href="/privacy">Politique de confidentialité</a></p>
</div>
</div>
<script>
function tp(){var f=document.getElementById('password');f.type=f.type==='password'?'text':'password';}
document.getElementById('lf').addEventListener('submit',function(){var b=document.getElementById('lsb');b.textContent='Connexion…';b.disabled=true;});
</script>
<style>
.auth-wrap{min-height:80vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;background:#faf9f5}
.auth-card{background:#fff;border:0.5px solid #ede8df;border-radius:20px;padding:40px;max-width:440px;width:100%}
.auth-head{text-align:center;margin-bottom:28px}
.auth-logo{width:48px;height:48px;border-radius:14px;background:#ede9fe;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:500;color:#6c63d4;margin:0 auto 16px;line-height:1}
.auth-title{font-family:Georgia,serif;font-size:26px;font-weight:400;color:#1e1208;margin-bottom:6px}
.auth-sub{font-family:'Manrope',sans-serif;font-size:13px;color:#8a7060;margin:0}
.auth-err{display:flex;align-items:center;gap:8px;background:#fce5df;border-radius:10px;padding:12px 14px;font-family:'Manrope',sans-serif;font-size:13px;color:#993c1d;margin-bottom:20px}
.af{margin-bottom:16px}
.al{font-family:'Manrope',sans-serif;font-size:12px;font-weight:500;color:#1e1208;display:block;margin-bottom:6px}
.ai{width:100%;padding:10px 14px;border:0.5px solid #ddd6c8;border-radius:10px;background:#faf9f5;font-family:'Manrope',sans-serif;font-size:13px;color:#1e1208;outline:none;box-sizing:border-box;transition:border-color 0.15s}
.ai:focus{border-color:#7c6cf0;background:#fff;box-shadow:0 0 0 3px rgba(124,108,240,.1)}
.auth-fgt{font-family:'Manrope',sans-serif;font-size:12px;color:#7c6cf0;text-decoration:none}
.auth-eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#a0907e;padding:4px;display:flex}
.auth-eye svg{width:15px;height:15px}
.auth-btn{width:100%;padding:11px;background:#7c6cf0;color:#fff;border:none;border-radius:10px;font-family:'Manrope',sans-serif;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.15s;margin-top:4px}
.auth-btn:hover{background:#6558d4}
.auth-btn:disabled{opacity:.65;cursor:not-allowed}
.auth-sep{display:flex;align-items:center;gap:12px;margin:24px 0}
.auth-sep::before,.auth-sep::after{content:'';flex:1;height:0.5px;background:#ede8df}
.auth-sep span{font-family:'Manrope',sans-serif;font-size:11px;color:#a0907e}
.auth-sw{text-align:center;font-family:'Manrope',sans-serif;font-size:13px;color:#6b5c4e;margin:0 0 14px}
.auth-sw a{color:#7c6cf0;text-decoration:none;font-weight:500}
.auth-legal{text-align:center;font-family:'Manrope',sans-serif;font-size:11px;color:#a0907e;line-height:1.7;margin:0;padding-top:16px;border-top:0.5px solid #ede8df}
.auth-legal a{color:#7c6cf0;text-decoration:none}
@media(max-width:500px){.auth-card{padding:28px 20px}}
</style>
