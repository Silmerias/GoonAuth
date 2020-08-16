<p>Congratulations!  By the powers vested in MembershipBot by God Emperor Lowtax, your access codes for the system has been approved!</p>
<p>You are a new member of the {{ $group }} group.</p>
<p>You are now cleared to join your fellow spergs in talking about dicks and butts.</p>
<hr>
<p>
  Your GoonID is: {{ $goonid }}<br>
  Your temporary password is: {{ $password }}
</p>
<hr>
<p>Your GoonID is your login ID for all of our services, be it the forums, Discord, wiki, etc.</p>
<p>
  The first thing you should do is <a href="{{ $app_url }}/account/chpasswd">
  change your password</a>.<br>  Your GoonID and password for all of our services
  are the same, so if you ever forget either of them,
  <a href="{{ $app_url }}/recover">use the recover service</a>.
</p>
<p>Your next step will be to log into the auth portal again and
  <a href="{{ $app_url }}/games">join an ingame organisation game</a>.
  Just click the <a href="{{ $app_url }}/games">"Join Game"</a>
  button and follow the directions.  If you are wondering why you still can't
  see specific discord or forum sections of your corporation, or why your ingame
  application has been rejected, you most likely didn't complete this step.
</p>
<p>Here are some handy links:<br>
  Membership Rules: <a href="{{ $app_rules }}">{{ $app_rules }}</a><br>
  Wiki: <a href="{{ $app_wiki }}">{{ $app_wiki }}</a><br>
  Auth Portal: <a href="{{ $app_url }}">{{ $app_url }}</a><br>
  Forum: <a href="{{ $app_forum }}">{{ $app_forum }}/</a><br>
  Discord: <a href="{{ $app_discord }}">{{ $app_discord }}</a>
</p>
