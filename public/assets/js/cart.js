// assets/js/cart.js
// functions used by pages: add-to-cart buttons, cart page, checkout page
function getCart() {
  try {
    return JSON.parse(localStorage.getItem('cart')||'[]');
  } catch(e) { return []; }
}
function saveCart(c) { localStorage.setItem('cart', JSON.stringify(c)); updateBadge(); }

function updateBadge(){
  const cart = getCart();
  const count = cart.reduce((s,i)=> s + (i.qty||0), 0);
  const b = document.getElementById('cart-count-badge');
  if (b) b.textContent = count;
}
updateBadge();

document.addEventListener('click', function(e){
  const t = e.target;
  if (t && t.matches('.add-to-cart')) {
    const id = t.dataset.id;
    const name = t.dataset.name;
    const price = parseFloat(t.dataset.price);
    // quantity input on product page
    let qty = 1;
    const qtyInput = document.getElementById('qty');
    if (qtyInput) qty = Math.max(1, parseInt(qtyInput.value)||1);

    const cart = getCart();
    const existing = cart.find(i => i.id == id);
    if (existing) existing.qty += qty;
    else cart.push({ id: id, name: name, price: price, qty: qty });
    saveCart(cart);
    alert('Added to cart');
  }
});

// Render cart on cart page
document.addEventListener('DOMContentLoaded', function(){
  const el = document.getElementById('cart-container');
  if (!el) return;
  const cart = getCart();
  if (cart.length === 0) {
    el.innerHTML = '<p>Your cart is empty.</p>';
    return;
  }
  const table = document.createElement('table');
  table.className = 'table';
  table.innerHTML = '<thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>';
  const tbody = document.createElement('tbody');
  let total = 0;
  cart.forEach((it, idx) => {
    const subtotal = it.price * it.qty;
    total += subtotal;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${it.name}</td><td>₦${it.price.toFixed(2)}</td>
      <td><input type="number" min="1" value="${it.qty}" data-idx="${idx}" class="form-control qty-input" style="max-width:80px;"></td>
      <td>₦${subtotal.toFixed(2)}</td>
      <td><button class="btn btn-sm btn-danger remove-item" data-idx="${idx}">Remove</button></td>`;
    tbody.appendChild(tr);
  });
  table.appendChild(tbody);
  const tfoot = document.createElement('div');
  tfoot.className = 'mt-3';
  tfoot.innerHTML = `<h4>Total: ₦${total.toFixed(2)}</h4>`;
  el.appendChild(table);
  el.appendChild(tfoot);

  el.addEventListener('change', function(e){
    if (e.target.matches('.qty-input')) {
      const idx = e.target.dataset.idx;
      let newQty = parseInt(e.target.value) || 1;
      const cart = getCart();
      cart[idx].qty = newQty;
      saveCart(cart);
      location.reload();
    }
  });

  el.addEventListener('click', function(e){
    if (e.target.matches('.remove-item')) {
      const idx = e.target.dataset.idx;
      const cart = getCart();
      cart.splice(idx,1);
      saveCart(cart);
      location.reload();
    }
  });
});