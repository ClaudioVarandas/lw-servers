// Storage options
let values = JSON.parse('[{"label":"0","value":0},{"label":"240GB","value":240},{"label":"500GB","value":500},{"label":"1TB","value":1024},{"label":"2TB","value":2048},{"label":"3TB","value":3072},{"label":"4TB","value":4096},{"label":"8TB","value":8192},{"label":"12TB","value":12288},{"label":"24TB","value":24576},{"label":"48TB","value":49152},{"label":"72TB","value":73728}]');

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

function handleParams(paramName, optionValue) {
  let params = new URLSearchParams(window.location.search)

  switch (paramName) {
    case 'storage':
      params.set(paramName, values[optionValue].value)
      break;
    default:
      params.set(paramName, optionValue)
      break;
  }

  let newParams = params.toString()
  window.history.pushState(null, null, '?' + newParams)
  document.location.reload()
}

// Storage options
$storageOptionsEl.addEventListener('change', function () {
  handleParams('storage', this.value)
});

// Location options
let $locationEl = document.getElementById('location')
$locationEl.addEventListener('change', function handleChange(event) {
  handleParams('location', this.value)
});

// Storage type options
let $storageTypeEl = document.getElementById('storage_type')
$storageTypeEl.addEventListener('change', function handleChange(event) {
  handleParams('storage_type', this.value)
});

// Ram options
let $ramCheckList = document.getElementById("ram_choices");
let $ramOptions = $ramCheckList.getElementsByTagName('input');
let len = $ramOptions.length;

for (let i = 0; i < len; i++) {
  if ($ramOptions[i].type === 'checkbox') {
    $ramOptions[i].onclick = function () {
      // I know there is a bug here somewhere :D , that puts empty value on the array
      let params = new URLSearchParams(window.location.search)

      let ramValues = params.get('ram[]')
      if (ramValues === null) {
        ramValues = new Array(this.value);
      } else {
        let oldRamValues = ramValues.split(',')

        if (this.checked) {
          console.log(this.value)
          oldRamValues.push(this.value)
        } else {
          const index = oldRamValues.indexOf(this.value);
          if (index > -1) {
            oldRamValues.splice(index, 1);
          }
        }
        ramValues = oldRamValues
      }

      params.set('ram[]', ramValues.toString())
      let newParams = params.toString()
      window.history.pushState(null, null, '?' + newParams)
      document.location.reload()
    };
  }
}
