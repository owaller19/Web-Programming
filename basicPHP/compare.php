<!DOCTYPE html>
<html>
<head>
	<title>London 2012 Olympics</title>
	<style>
    body {
	    display: flex;
	    flex-direction: column;
	    align-items: center;
        background-image: url("olympic_back.jpg");
        background-repeat: no-repeat;
        background-size: cover;
	  }
	  .form-container {
	    display: flex;
        justify-content: center;
        margin: 30px;
        gap: 50px;
	  }
      .compareMedals, .globalRank {
	    border: 2px solid black;
	    padding: 10px;
	    margin: 10px;
        margin-top: 50px;
        width: 500px;
	    display: flex;
	    flex-direction: column;
	    align-items: center;
	    justify-content: center;
        color: white;
      }
	  .compareMedals {
	    margin-right: 10px;
        background-image: url("medal.jpg");
        background-size: cover;
	  }
	  .globalRank {
	    margin-left: 10px;
        background-image: url("ranking.jpg");
        background-size: cover;
	  }
      h1 {
	    text-align: center;
        text-decoration: underline;
        background-color: red;
        color: white;
        font-size: 50px;
	  }
    .submitBut {
      cursor: pointer;
    }
    .submitBut:hover {
      background-color: red;
    }
	</style>
</head>
<body>
	<h1>2012 Olympic Comparisons</h1>
	<div class="form-container">
	  <form class = "compareMedals", action = "medals.php", method = "get", onsubmit="return validateForm(this, 'country1', 'country2')">
	    <h2>Compare Medals</h2>
	    <label for="country1">Enter ID of first country</label>
	    <input type="text" id="country1" name="country1"><br>
	    <label for="country2">Enter ID of second country</label>
	    <input type="text" id="country2" name="country2"><br>
	    <input class= "submitBut" type="submit" value="Compare">
	  </form>

	  <form class="globalRank", action = "globalRank.php", method = "get", onsubmit= "return validateForm(this, 'country3', 'country4')">
	    <h2>Global Rankings</h2>
	    <label for="country3">Enter ID of first country</label>
	    <input type="text" id="country3" name="country3"><br>
	    <label for="country4">Enter ID of second country</label>
	    <input type="text" id="country4" name="country4"><br>
	    <input class= submitBut type="submit" value="See Rankings">
	  </form>

	</div>
    <script>
        function validateForm(form, country1FieldName, country2FieldName) {
            var country1 = form.elements[country1FieldName].value;
            var country2 = form.elements[country2FieldName].value;

            if (country1 == country2) {
                alert("Please enter two different IDs.");
                return false;
            } else {
                sendRequest(country1, country2, form);
                return false; 
            }
        }

        function sendRequest(country1, country2, form) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText === "exists") {
                        form.submit();
                    } else {
                        alert("One or both country IDs do not exist in the table.");
                    }   
                }
            };
            xhttp.open("GET", "validateIDs.php?country1=" + country1 + "&country2=" + country2, true);
            xhttp.send();
        }

	</script>
</body>
</html>
