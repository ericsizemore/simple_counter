<pre>
/**
* @author    Eric Sizemore &lt;admin@secondversion.com&gt;
* @package   SV's Simple Counter
* @link      http://www.secondversion.com
* @version   2.0.1
* @copyright (C) 2006 - 2012 Eric Sizemore
* @license   GNU Lesser General Public License
*
*	SV's Simple Counter is free software: you can redistribute it and/or modify
*	it under the terms of the GNU Lesser General Public License as published by
*	the Free Software Foundation, either version 3 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful, but WITHOUT 
*	ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
*	FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more 
*	details.
*
*	You should have received a copy of the GNU Lesser General Public License
*	along with this program.  If not, see &lt;http://www.gnu.org/licenses/&gt;.
*/
</pre>


<h2>What is SV's Simple Counter?</h2>
It is a simple PHP counter that counts your website visitors. It has the ability to 
either show the count as plain text, or images; and whether or not to count only 
unique hits, or all hits. (IP Based)

<h2>Installation</h2>
<ol>
	<li>Open 'counter.php', configure the settings near the top of the script.</li>
	<li>Create a new folder, named 'counter'.</li>
	<li>Upload 'counter.php', 'index.html', and the 'logs' directory in ASCII mode.</li>
	<li>Upload the 'images' folder in BINARY mode.</li>
	<li>CHMOD the 'counter.txt' and 'ips.txt' files to 0666. They are located in the 'logs' folder.</li>
</li>

<h2>Usage</h2>
Simple add the following code to the page where you want the counter to be shown:<br />
<code>
<?php

include('./counter/counter.php');

?>
</code>
<br />
<br />
That's pretty much it, really.