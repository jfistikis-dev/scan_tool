
<?=$this->extend("common/main")?>

<?=$this->section("content")?>
	
	
	<div class="row" data-masonry="{&quot;percentPosition&quot;: true }" >
		
		<div class="col-sm-6 col-lg-6 mb-6 p-4 " id="sales">
		  <div class="card text-white text-center p-5 front-card">
			<figure class="mb-0">
			  <blockquote class="blockquote">
				<p>ΤΑΜΕΙΟ</p>
			  </blockquote>
			  <figcaption class="blockquote-footer mb-0 text-white">
				Ένα απλό <cite title="Source Title">Point-of-Sales</cite> για την Λιανική
			  </figcaption>
			</figure>
		  </div>
		</div>
		
		<div class="col-sm-6 col-lg-6 mb-6 p-4" id="imports">
		  <div class="card text-white text-center p-5 front-card">
			<figure class="mb-0">
			  <blockquote class="blockquote">
				<p>ΠΡΟΜΗΘΕΙΕΣ</p>
			  </blockquote>
			  <figcaption class="blockquote-footer mb-0 text-white">
				<cite title="Source Title">Αγορά/Μεταβολή</cite>  προϊόντων 			  
			</figcaption>
			</figure>
		  </div>
		</div>

		<div class="col-sm-6 col-lg-6 mb-6 p-4" id="products">
		  <div class="card text-white text-center p-5 front-card">
			<figure class="mb-0">
			  <blockquote class="blockquote">
				<p>ΠΡΟΪΌΝΤΑ</p>
			  </blockquote>
			  <figcaption class="blockquote-footer mb-0 text-white">
				Εμφάνιση <cite title="Source Title">προϊόντων</cite> σε λίστα
			  </figcaption>
			</figure>
		  </div>
		</div>
        <?php /*
		<div class="col-sm-6 col-lg-6 mb-6 p-4" id="stats">
		  <div class="card text-white text-center p-5 front-card">
			<figure class="mb-0">
			  <blockquote class="blockquote">
				<p>ΣΤΑΤΙΣΤΙΚΑ</p>
			  </blockquote>
			  <figcaption class="blockquote-footer mb-0 text-white">
				Someone famous in <cite title="Source Title">Source Title</cite>
			  </figcaption>
			</figure>
		  </div>
		</div>
		    */ ?>
	
	</div>
		
		
	
		
		
	<script>
	
		
	$("#sales").on("click", function() { window.location.href = 'sales';});
	$("#imports").on("click", function() {window.location.href = 'supplies';});
	$("#products").on("click", function() {window.location.href = 'products';});
	//$("#stats").on("click", function() {window.location.href = 'stats';});
	
	
	</script>

<?=$this->endSection()?>