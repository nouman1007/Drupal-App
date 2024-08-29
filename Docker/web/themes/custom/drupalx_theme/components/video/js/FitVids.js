// Assuming players is defined somewhere above
if (!Array.isArray(players)) {
  console.error('Expected players to be an array, but got:', players);
  players = []; // Initialize as an empty array if not
}

// Proceed with the join operation
var playerIds = players.join(','); // This should now work without error

// Check if playerIds is not empty before querying
if (playerIds) {
  // Select videos
  var fitVids = document.querySelectorAll(playerIds);

  // If there are videos on the page...
  if (fitVids.length) {
      // Loop through videos
      for (var i = 0; i < fitVids.length; i++) {
          // Get Video Information
          var fitVid = fitVids[i];
          var width = fitVid.getAttribute("width");
          var height = fitVid.getAttribute("height");
          var aspectRatio = height / width;
          var parentDiv = fitVid.parentNode;

          // Wrap it in a DIV
          var div = document.createElement("div");
          div.className = "fitVids-wrapper";
          div.style.paddingBottom = aspectRatio * 100 + "%";
          parentDiv.insertBefore(div, fitVid);
          fitVid.remove();
          div.appendChild(fitVid);

          // Clear height/width from fitVid
          fitVid.removeAttribute("height");
          fitVid.removeAttribute("width");
      }
  }
} else {
  console.error('No valid player IDs found for querySelectorAll.');
}