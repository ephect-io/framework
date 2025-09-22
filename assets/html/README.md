# How to remake the HTML logo

- Go to https://www.text-image.com/convert/,
- Select the file logo-reflexion.png from images folder,
- Copy HTML result in a new HTML page,
- Remove the additional black pixel with the substitution regex: 

```regexp
search: `(?:\G|(<b style="color:#000000">))█`
```
```regexp
replace: `$1 `
```

The regex can be found at https://regex101.com/r/cd0Ttz/1
