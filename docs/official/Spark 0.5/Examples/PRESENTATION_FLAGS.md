## Spark 0.5 - PRESENTATION_FLAGS

The presentation flags are a number of flags and values that changes the way Spark CLI outputs data.
They are used for user convenience and for fine-tuning the tool - for example for execution manually vs in automated scripts.
<br><br>

flag: `auto`<br>
Removes the colors, for use in scripting.<br>
In PHP CLI, the console colors are special command characters that we output to change the color.<br>
This flags stops this behavior, so that no color command characters are outputted.<br>
syntax (all are valid): `auto`, `a`, `-a`, `-auto`, `--auto`
<br><br>

flag: `hide-banner`<br>
Removes the ASCII art banner, for use in scripting.<br>
syntax (all are valid): `hide-banner`, `hb`, `-hidebanner`, `-hb`, `--hide-banner`
<br><br>

flag: `theme=`<br>
Sets the color theme of the CLI text output.<br>
syntax: `theme=<THEME>`<br>
THEME: `GREEN`, `DBLUE`, `LBLUE`, `PASTEL`, `EARTH`, `CONTRAST`, `DEFAULT`, `VIOLET`, `CYAN`
<br><br>
