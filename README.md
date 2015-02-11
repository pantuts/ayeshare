# ayeshare
Basic, simple, and minimalist file sharing service.

The MIT License (MIT)  
Copyright (c) 2015 pantuts

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.  
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.  

Features:  
1. Selected file will be auto-uploaded.
2. Download file within valid time.
3. Send link/s to friends.
4. Security aware. Scripts like php or perl will be treated as text/plain.
5. and more.  

HowTo:
`git clone https://github.com/pantuts/ayeshare`  
* Copy all the files to your webserver and make sure you have the right access.  
* Configure you `php.ini` for `upload_max_filesize` and `post_max_size` to your required upload size (Ex: 20M).  
* Set your `config.ini` to your desired settings.
* Make sure you also copied the `.htaccess` in same directory.
* Now navigate to your `localhost/ayeshare` then start using the system.  

Disclaimer:  
I'm not an expert. Thanks. :)
