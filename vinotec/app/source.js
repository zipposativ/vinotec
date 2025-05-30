var loadFile = function(event) {
  var image = document.getElementById('image_output');
  image.src = URL.createObjectURL(event.target.files[0]);
};
