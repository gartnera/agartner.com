+++
title = "gamescope-mode-manager"
date = "2024-12-27"
tags = [
    "linux",
]
+++

Gaming on linux (wayland/sway) has worked relatively well for years now (aside from anti-cheat issues). I run all my games under [gamescope](https://github.com/ValveSoftware/gamescope) to avoid X11 glitches and ensure [rendering at native resolution](https://github.com/swaywm/sway/issues/1047). But I did have two issues that I kept running into:

1) Accidentally pressing system keybindings. I'd often press MOD+D by accident which is my keybinding for my task launcher.
2) Unable to set push to mute in discord[^1]. When you push to talk in game, you want discord to mute your input so you don't double send.

I've solved both of these problems with [gamescope-mode-manager](https://github.com/gartnera/.linux-config/blob/cc8474975f5e8450b550922e0a08ec1f64de1033/scripts-bin/gamescope-mode-manager). This program listens for i3[^2] IPC events and watches for specific app IDs to become focused. A sway IPC call is then made to activate my custom [gamescope](https://github.com/gartnera/.linux-config/blob/cc8474975f5e8450b550922e0a08ec1f64de1033/dotfiles/links/.config/sway/config#L203-L247) mode which:

1) Limits the number of bound symbols to avoid accidental presses.
2) Configures SHIFT+F12 to toggle mangohud visibility via `mangohudctl toggle no_display`.
3) [Mutes chromium input](https://github.com/gartnera/.linux-config/blob/cc8474975f5e8450b550922e0a08ec1f64de1033/scripts-bin/chrome-input-mute) when `t` or mouse side button (`button8`) is pressed. I run discord in chromium.

In order to accomplish the last step, I needed to add a `--passthrough` argument to the `bindsym` directive of sway. This [small patch](https://github.com/gartnera/sway/commit/c59a598f1c9807497f3dd954a784174b38b5fb21) allows you to both run a command AND send the input to the application.

[^1]: The wayland protocols do not allow global key bindings. Even if it did it would take years to the compositors and applications to actually roll out support.
[^2]: `sway` is mostly compatible with `i3` IPC. That's why I can use the `i3ipc` package.