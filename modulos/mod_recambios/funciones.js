// JavaSCRIPT para modulo de recambios.
function copiarAlPortapapeles(id_elemento) {
  document.execCommand("delete");
  console.log('Ver si entra:'+id_elemento);
  //~ var aux;
  var aux = document.getElementById(id_elemento);
  aux.select();
  document.execCommand("copy");
  console.log(aux.value);
  //~ document.execCommand("copy");
  
  
  
  // document.body.removeChild(aux);
}
