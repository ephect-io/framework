# How to make a terminal logo

- Select HTML file resulting from HTML conversion steps

```bash
  php .\html_to_ansi.php ..\html\ephect.html
```

- Replace a&& characters with a cursor character (e.g. `█`) in the resulting ANSI file

```bash
  sed -i 's/â/█/g' ..\html\ephect.ansi
  ```

  