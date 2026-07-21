<h1>Pesan baru dari portofolio</h1>

<p><strong>Nama:</strong> {{ $messageData['name'] }}</p>
<p><strong>Email:</strong> {{ $messageData['email'] }}</p>
<p><strong>Subjek:</strong> {{ $messageData['subject'] }}</p>

<h2>Pesan</h2>
<p>{!! nl2br(e($messageData['message'])) !!}</p>
