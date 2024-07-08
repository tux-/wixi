# Wixi

A very simple wiki that stores pages as individual xml files on disk. There is no dependencies on any database.

Wixi uses [Editor.js](https://editorjs.io/) | [github](https://github.com/codex-team/editor.js) as frontend editing interface.

![Screenshot of the default wiki frontpage](screenshot.png)

## Worth of notice

* There is no authentication. Use on internal network only.
* There is no version history. Take regular backups of the data directory.
* Search is just a wrapper based on grep.
* There is no page to list all created pages.
* There is no UI to change the slug of a page or delete it. You have to use the file system directly for this.
* The project was created just to fill a need. More features will be added based on need, feedback or contributions.

## Installation

```bash
git clone --recursive https://github.com/tux-/wixi
```

Copy `config.example.ini` to `config.ini` and edit values for your setup.

### Storage locations
Storage locations can be individually configured. The default `jail` will keep them in the site installation root. To create and give access to these folder you can use something like:

```bash
sudo mkdir temp cache storage && sudo chown www-data:users temp cache storage && sudo chmod 6775 temp cache storage
```

### Apache

Use a rewrite rule something like this:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php/$1 [L]
```

If you want to share write access to the stored data, you can use `mpm_itk` to utilize the `users` group:
```apache
<IfModule mpm_itk_module>
	AssignUserId www-data users
</IfModule>
```

## Usage notes

### Pasted content
When pasting content from another source, the generated code is not always correct. Save and reload the page to check that the data is correct.

### Open link
Since normal clicking of links places the cursor in that link, the `Shift` + click is hijacked to open link in the same window instead of a new one. `Ctrl` and `Ctrl + Shift` click works as normal. (Unknown what would happen on a mac).

### Internal links
The wiki support two protocols for internal links. If you paste internal links into the href input of a link, they will automatically be detected and converted. This makes it very easy to migrate the site to a new location without having to change any of the saved documents.

* `base://` relative to the base path of the installation.
* `wiki://` relative to the wiki part.

### Paths

`/wiki` this is the root of the wiki itself. All pages must be added under this path, eg: `/wiki/mytopic`. You can create as many levels as you wish. The slug will be used as file name for the xml file containing the data. `/` and `/wiki` both loads the wiki index page.
