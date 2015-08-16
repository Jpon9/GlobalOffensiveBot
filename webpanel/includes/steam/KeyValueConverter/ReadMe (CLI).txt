Copywrite © 2014 SteamToolbox.com

KeyValueConverter is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

KeyValueConverter is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with KeyValueConverter.  If not, see <http://www.gnu.org/licenses/>.

+--------------------------------------------+
|             Quick Information              |
+--------------------------------------------+
| Title        KeyValueConverter             |
| Author       Jake                          |
| Website      SteamToolbox.com              |
| Email        jake@steamtoolbox.com         |
+--------------------------------------------+

Description
	KeyValueParser takes a Valve style KeyValue file and turns it into
	a JSON-formatted file with no added whitespace/indentation. This
	is specifically for parsing data for use on web-based statistics in
	tandem with the information provided from the Steam Web API.

Usage
	To use, edit the files.txt files with a list of absolulte filepaths
	for the source files and output files.  Source files should be text
	files with the KeyValue data unaltered.  Destination files should be
	JSON files in a location you are familiar with.  The file format for
	files.txt is as follows:
	
		SOURCE
		C:\this\is\an\example\path.txt
		C:\this\is\another\example.txt
		C:\Program Files (x86)\Steam\steamapps\common\Counter-Strike Global Offensive\csgo\resource\csgo_english.txt
		
		DESTINATION
		C:\keyvalueparseroutput\path.json
		C:\keyvalueparseroutput\example.json
		C:\keyvalueparseroutput\csgo_english.json
		
		TARGET
		2
		
		INDENT
		true
	
	>>> THE NUMBER OF DESTINATION FILES AND THE NUMBER OF SOURCE FILES MUST MATCH! <<<
	The target is the file you want to target when you launch the tool with
	1 being the first file and 3 being the last file, example:
	
		1 = C:\this\is\an\example\path.txt
		2 = C:\this\is\another\example.txt
		3 = C:\Program Files (x86)\Steam\steamapps\common\Counter-Strike Global Offensive\csgo\resource\csgo_english.txt
	
	Alternatively, if the line following "TARGET" is any alphanumeric value
	that cannot be converted into a number, all of the files will be converted.
	
	The line following "INDENT" should be either "true" or "false", case insensitive.
	Anything other than "true" or "false" will be treated as "false"
DEPENDENCIES / LICENSING
	This program relies on the .NET framework and the Json.NET library.
	