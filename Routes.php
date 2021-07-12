<?php 

Route::set('signout', function() {
	
	SignOut::CreateView('SignOut');
});
Route::set('signin', function() {
		SignIn::CreateView('SignIn');
});
$variable = '';
Route::set($variable, function() {
	Market::CreateView('Market');
});
Route::set('home', function() {
	
	Market::CreateView('Market');
		
});

Route::set('forgot-password', function() {
		ForgotPassword::CreateView('ForgotPassword');
});

Route::set('change-password', function() {
		
		ChangePassword::CreateView('ChangePassword');
});




Route::set('create-account', function() {
		
		CreateAccount::CreateView('CreateAccount');
});
Route::set('messages', function() {
		
		Messages::CreateView('Messages');
});

Route::set('products', function() {
		
		Products::CreateView('Products');
});

Route::set('market', function() {
		
		Market::CreateView('Market');
});

?>