function nice_r_toggle(pfx, id) {
  var elp = document.getElementById(pfx + '_v' + id);
  var elc = document.getElementById(pfx + '_a' + id);
  if (elp) {
    if (elp.style.display === 'block' || elp.style.display=='') {
        elp.style.display = 'none';
        if (elc) elc.innerHTML = '&#9658;';
    } else {
        elp.style.display = 'block';
        if (elc) elc.innerHTML = '&#9660;';
    }
  }
}

function raw_toggle() {
  var rrr=document.getElementById('raw_print_r');
  if (rrr.style.display === 'block' || rrr.style.display=='') {
      rrr.style.display = 'none';
  } else {
      rrr.style.display = 'block';
  }
}

