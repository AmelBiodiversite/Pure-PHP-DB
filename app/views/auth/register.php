<?php /* MARKETFLOW PRO — INSCRIPTION */ ?>
<div class="auth-wrap">
<div class="auth-card" style="max-width:500px">
  <div class="auth-head">
    <div class="auth-logo">M</div>
    <h1 class="auth-title">Créer un compte</h1>
    <p class="auth-sub">Rejoignez la marketplace des créateurs</p>
  </div>
  <?php if (isset($errors['general'])): ?>
  <div class="auth-err">
    <svg viewBox="0 0 16 16" fill="none" width="15" height="15"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.4"/><path d="M8 5v3.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><circle cx="8" cy="11" r=".6" fill="currentColor"/></svg>
    <?= e($errors['general']) ?>
  </div>
  <?php endif; ?>
  <form method="POST" action="/register" id="rf">
    <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
    <div class="af">
      <label class="al">Type de compte</label>
      <div class="rtype-grid">
        <label class="rtype-card active" id="rc-buyer">
          <input type="radio" name="role" value="buyer" checked style="display:none">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
          <span class="rtype-nm">Acheteur</span>
          <span class="rtype-ds">Acheter des produits</span>
        </label>
        <label class="rtype-card" id="rc-seller">
          <input type="radio" name="role" value="seller" style="display:none">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 17.5h7M17.5 14v7"/></svg>
          <span class="rtype-nm">Vendeur</span>
          <span class="rtype-ds">Vendre vos créations</span>
        </label>
      </div>
    </div>
    <div class="af">
      <label class="al" for="full_name">Nom complet</label>
      <input type="text" id="full_name" name="full_name"
        class="ai<?= isset($errors['full_name']) ? ' ai-err' : '' ?>"
        placeholder="Jean Dupont"
        value="<?= isset($old['full_name']) ? e($old['full_name']) : '' ?>" autofocus>
      <?php if (isset($errors['full_name'])): ?><span class="ferr"><?= e($errors['full_name']) ?></span><?php endif; ?>
    </div>
    <div class="af">
      <label class="al" for="email">Adresse email</label>
      <input type="email" id="email" name="email"
        class="ai<?= isset($errors['email']) ? ' ai-err' : '' ?>"
        placeholder="vous@example.com"
        value="<?= isset($old['email']) ? e($old['email']) : '' ?>" required>
      <?php if (isset($errors['email'])): ?><span class="ferr"><?= e($errors['email']) ?></span><?php endif; ?>
    </div>
    <div class="af">
      <label class="al" for="username">Nom d'utilisateur</label>
      <input type="text" id="username" name="username"
        class="ai<?= isset($errors['username']) ? ' ai-err' : '' ?>"
        placeholder="jeandupont"
        value="<?= isset($old['username']) ? e($old['username']) : '' ?>" required>
      <span class="fhint">Lettres, chiffres, tirets et underscores</span>
      <?php if (isset($errors['username'])): ?><span class="ferr"><?= e($errors['username']) ?></span><?php endif; ?>
    </div>
    <div class="af" id="shopGrp" style="display:none">
      <label class="al" for="shop_name">Nom de votre boutique</label>
      <input type="text" id="shop_name" name="shop_name"
        class="ai<?= isset($errors['shop_name']) ? ' ai-err' : '' ?>"
        placeholder="Ma Super Boutique"
        value="<?= isset($old['shop_name']) ? e($old['shop_name']) : '' ?>">
      <?php if (isset($errors['shop_name'])): ?><span class="ferr"><?= e($errors['shop_name']) ?></span><?php endif; ?>
    </div>
    <div class="af">
      <label class="al" for="password">Mot de passe</label>
      <div style="position:relative">
        <input type="password" id="password" name="password"
          class="ai<?= isset($errors['password']) ? ' ai-err' : '' ?>"
          style="padding-right:40px" placeholder="••••••••" required>
        <button type="button" class="auth-eye" onclick="tp('password')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>
      <span class="fhint">Minimum 8 caractères</span>
      <?php if (isset($errors['password'])): ?><span class="ferr"><?= e($errors['password']) ?></span><?php endif; ?>
    </div>
    <div class="af">
      <label class="al" for="password_confirm">Confirmer le mot de passe</label>
      <div style="position:relative">
        <input type="password" id="password_confirm" name="password_confirm"
          class="ai<?= isset($errors['password_confirm']) ? ' ai-err' : '' ?>"
          style="padding-right:40px" placeholder="••••••••" required>
        <button type="button" class="auth-eye" onclick="tp('password_confirm')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>
      <?php if (isset($errors['password_confirm'])): ?><span class="ferr"><?= e($errors['password_confirm']) ?></span><?php endif; ?>
    </div>
    <div class="af" style="margin-top:6px">
      <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-family:'Manrope',sans-serif;font-size:12px;color:#6b5c4e;line-height:1.6">
        <input type="checkbox" name="terms" required style="margin-top:2px;accent-color:#7c6cf0;flex-shrink:0">
        <span>J'accepte les <a href="/terms" target="_blank" style="color:#7c6cf0;text-decoration:none">CGU</a> et la <a href="/privacy" target="_blank" style="color:#7c6cf0;text-decoration:none">Politique de confidentialité</a></span>
      </label>
    </div>
    <button type="submit" class="auth-btn" id="rsb" style="margin-top:10px">Créer mon compte</button>
  </form>
  <div class="auth-sep"><span>ou</span></div>
  <p class="auth-sw">Déjà un compte ? <a href="/login">Se connecter →</a></p>
</div>
</div>
<script>
function tp(id){var f=document.getElementById(id);f.type=f.type==='password'?'text':'password';}
document.addEventListener('DOMContentLoaded',function(){
  var bc=document.getElementById('rc-buyer'),sc=document.getElementById('rc-seller'),sg=document.getElementById('shopGrp'),si=document.getElementById('shop_name');
  [bc,sc].forEach(function(c){c.addEventListener('click',function(){bc.classList.remove('active');sc.classList.remove('active');this.classList.add('active');var r=this.querySelector('input[type="radio"]');r.checked=true;if(r.value==='seller'){sg.style.display='block';si.required=true;}else{sg.style.display='none';si.required=false;}});});
  document.getElementById('rf').addEventListener('submit',function(e){
    var pw=document.getElementById('password').value,pc=document.getElementById('password_confirm').value;
    if(pw!==pc){e.preventDefault();alert('Les mots de passe ne correspondent pas');return;}
    if(pw.length<8){e.preventDefault();alert('Mot de passe trop court (8 caractères min.)');return;}
    var b=document.getElementById('rsb');b.textContent='Création…';b.disabled=true;
  });
});
</script>
<style>
.rtype-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.rtype-card{border:0.5px solid #ddd6c8;border-radius:12px;padding:14px 12px;cursor:pointer;text-align:center;transition:all 0.15s;display:flex;flex-direction:column;align-items:center;gap:5px}
.rtype-card svg{width:18px;height:18px;color:#8a7060}
.rtype-card.active{border-color:#7c6cf0;background:#f5f3ff}
.rtype-card.active svg{color:#534ab7}
.rtype-nm{font-family:'Manrope',sans-serif;font-size:13px;font-weight:500;color:#1e1208}
.rtype-ds{font-family:'Manrope',sans-serif;font-size:11px;color:#a0907e}
.ai-err{border-color:#d85a30!important}
.fhint{font-family:'Manrope',sans-serif;font-size:11px;color:#a0907e;display:block;margin-top:4px}
.ferr{font-family:'Manrope',sans-serif;font-size:11px;color:#993c1d;display:block;margin-top:4px}
</style>
