// Storage options
let values = JSON.parse('[{"label":"0","value":0},{"label":"250GB","value":250},{"label":"500GB","value":500},{"label":"1TB","value":1024},{"label":"2TB","value":2048},{"label":"3TB","value":3072},{"label":"4TB","value":4096},{"label":"8TB","value":8192},{"label":"12TB","value":12288},{"label":"24TB","value":24576},{"label":"48TB","value":49152},{"label":"72TB","value":73728}]');

let $storageOptionsEl = document.getElementById('storage_options'),
  $storageOptionsLabelEl = document.getElementById('storage_options_label');
$storageOptionsEl.oninput = function () {
  for (let key in values) {
    if (key === this.value) {
      $storageOptionsLabelEl.innerHTML = values[key].label;
    }
  }
};
$storageOptionsEl.oninput();

$storageOptionsEl.addEventListener('change',function (){
  const params = new URLSearchParams(window.location.search)
  params.set('storage', values[this.value].value)
  let newParams = params.toString()
  window.history.pushState(null, null, '?' + newParams)
  document.location.reload()
});

// Location options
let $locationEl = document.getElementById('location')
$locationEl.addEventListener('change', function handleChange(event) {
  const params = new URLSearchParams(window.location.search)
  params.set('location', this.value)
  let newParams = params.toString()
  window.history.pushState(null, null, '?' + newParams)
  document.location.reload()
});

// Storage type options
let $storageTypeEl = document.getElementById('storage_type')
$storageTypeEl.addEventListener('change', function handleChange(event) {
  const params = new URLSearchParams(window.location.search)
  params.set('storage_type', this.value)
  let newParams = params.toString()
  window.history.pushState(null, null, '?' + newParams)
  document.location.reload()
});

// Ram options
let $ramCheckList = document.getElementById("ram_choices");
let $ramOptions = $ramCheckList.getElementsByTagName('input');
let len = $ramOptions.length;

for (let i = 0; i < len; i++) {
  if ($ramOptions[i].type === 'checkbox') {
    $ramOptions[i].onclick = function (){
      //const params = new URLSearchParams(window.location.search)

      //const values = Array.from(params.values());
      //const entries = Array.from(params.entries())
      //const hasRam = params.has('ram');
      //console.log(entries)


//      let newParams = params.toString()
//      window.history.pushState(null, null, '?' + newParams)
//      document.location.reload()
    };
  }
}
