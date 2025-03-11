<!DOCTYPE html>
<html>
<head>
    <title>Email Search</title>
</head>
<body>
<h1>Search Emails</h1>
<form action="{{ url('/find-emails') }}" method="post">
    @csrf
    <label>Name:</label>
    <input type="text" name="name">
    <br>
    <label>Company:</label>
    <input type="text" name="company">
    <br>
    <label>LinkedIn URL:</label>
    <input type="url" name="linkedinUrl">
    <br>
    <button type="submit">Search</button>
</form>
</body>
</html>
