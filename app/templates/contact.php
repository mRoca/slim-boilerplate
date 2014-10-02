<?php

SlimBoilerplate\Layout\LayoutView::addJs('assets/js/contact.js');

SlimBoilerplate\Layout\LayoutView::setTitle('Contact');
// SlimBoilerplate\Layout\LayoutView::setDescription('This is my description');
// SlimBoilerplate\Layout\LayoutView::setKeywords('This, is, my, keywords');

?>


<h2>Contact</h2>

<?php if(! isset($success) && ! isset($error)): ?>
	<div class="gmap" id="maps"></div>
<?php endif; ?>

<form action="<?= url('contact') ?>" method="post" class="contact" id="contact-form">

	<?php if(isset($success) && $success): ?>
		<div id="contact-validation-success" class="success">Votre message a été envoyé avec succès.</div>
	<?php endif; ?>

	<?php if(isset($error)): ?>
		<div id="contact-validation-error" class="error">Erreur lors de l'envoi du message : <?= $error ?></div>
	<?php endif; ?>

	<label for="contact_name">Votre nom <sup>*</sup></label>
	<input type="text" name="contact_name" id="contact_name" value="<?= formValue('contact_name') ?>" />
	<br />
	<label for="contact_email">Votre email <sup>*</sup></label>
	<input type="text" name="contact_email" id="contact_email" value="<?= formValue('contact_email') ?>" />
	<br />
	<label for="contact_company">Votre société <sup>*</sup></label>
	<input type="text" name="contact_company" id="contact_company" value="<?= formValue('contact_company') ?>" />
	<br />
	<label for="contact_message">Votre message <sup>*</sup></label>
	<textarea name="contact_message" id="contact_message" rows="10"><?= formValue('contact_message') ?></textarea>
	<br />
	<input type="submit" value="Envoyer l'email">

	<div id="validation-error" class="error" style="display: none;">Tous les champs sont requis.</div>
</form>
