// JavaSCRIPT para modulo de recambios.
function copiarAlPortapapeles(id_elemento) {
  //~ document.execCommand("delete")
  document.execCommand("delete");
  console.log('Ver si entra:'+id_elemento);
  //~ var aux;
  var aux = document.getElementById(id_elemento);
  aux.select();
  console.log(aux.value);
  document.execCommand("copy");
}
