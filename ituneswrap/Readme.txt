***************iTunes XML Wrapper Component**********

Copyright (c) 2006, Lacy MorrowAll rights reserved.Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.    * Neither the name of the author nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.


The iTunes XML Wrapper is a data wrapper for an exported iTunes library or playlist file. Save time on coding the xml parsing, just insert this component and use "loadXML(FILE NAME)" to load a file. All data is output to the root, in an array "ItunesArray".
This uses the V2 Component Architecture and must be published for Flash player 6.0.r65 or greater.


HOW TO USE:
To export a library or playlist in iTunes, select the source, click file, then export. Be sure to choose xml as the type.

Data is output to "ItunesArray"
Retrieve it by using "ItunesArray[TRACKNUMBER].DATASET
ex: "ItunesArray[1].Name"
You can retrieve data by using the following sets:
TrackId
Name
Artist
Composer
Album
Genre
Kind
Size
Time
DiscNumber
DiscCount
TrackNumber
TrackCount
Year
DateModified
DateAdded
BitRate
SampleRate
Comments
PlayCount
PlayDate
Compilation
TrackType
Location