+++
title = "Long-Term Software Forks"
date = "2025-01-19"
tags = []
+++

This is a catalog of some of the software forks that I've maintained for years. These forks happen when the upstream repository is unable or unwilling to accept our changes. I have an unfortunate amount of experience with maintaining forks for a long time (mostly at work).

## Long Term Forks

### [AeroSpace](https://github.com/gartnera/AeroSpace)

I've been enjoying using the [AeroSpace](https://github.com/nikitabobko/AeroSpace) window manager on my work mac. However, [my pull request](https://github.com/nikitabobko/AeroSpace/pull/805) for making menu bar indicator monospace was rejected. The developer seems quite opinionated about how to do things so I suspect I will have to maintain this fork for awhile.

I've also implemented automatic rebasing on this repo which I will describe below.

### [swaylock](https://github.com/gartnera/swaylock)

The original display power management (DPMS) [pull request](https://github.com/swaywm/swaylock/pull/152) was rejected. I prefer this behavior so I've been manually rebasing it for ~3 years now. There aren't that many changes to swaylock and usually only requires a rebase when a new version of sway is released (yearly). I also provide an [Arch User Repository (AUR) package](https://aur.archlinux.org/packages/swaylock-dpms-git) which makes installing it easy.

### [protobuf-go](https://github.com/braincorp/protobuf-go/tree/v1.31.0-brain)

protobuf-go will return an error (which ultimately cause a panic) if you use underscores in protojson representation. This was ultimately a really bad idea on our part. But after you do the first stable release of a service it will be very hard to change the style (especially in a small company in the IOT space).

### [go-ethereum](https://github.com/zeta-chain/go-ethereum/commits/release/1.13-zeta/)

go-ethereum is the main Ethereum Virtual Machine (EVM) implementation for the go programming language. We run an EVM compatible chain at zeta-chain which requires an EVM implementation. But go-ethereum will not add support for features that ethereum does not want/use. So most chains have to maintain a fork.

We specifically need precompiled smart contracts which allow smart contract developers to interact with custom logic we have written in go. There is a [medium sized patch](https://github.com/ethereum/go-ethereum/commit/c6c6d165b8772d2b372a49ec43519dc667285642) that we initially used to provide this functionality. However, it touched way too many files unnecessarily. I rewrote this patch to be [much smaller](https://github.com/zeta-chain/go-ethereum/commit/31ce551954afc11684d23096df94536055254306). Because of this it will be easier to rebase in the future.

### [WhichSpace](https://github.com/gartnera/WhichSpace)

WhichSpace is a menu bar indicator which will show you which space is active. It was broken on new versions of MacOS but there was a pull request fixing the issue. I cherry picked that change to my fork and create a signed build with my Apple Developer account.

## Rebase vs Merge

Typically a fork is pretty painful to maintain because you have to merge any upstream changes with your local changes. If you merge your changes on your fork, you very quickly lose track of your commits in the git history. It needs to be very clear what you changed so when you come back to the repo a year+ later it's very obvious what you changed.

## Automatic Rebase

I've implemented automatic rebasing on my [AeroSpace](https://github.com/gartnera/AeroSpace) fork to avoid having to manually rebase and rebuild it every time. This will ensure that my patch set is applied and new binaries are build without any user interaction.

You can use a [scheduled github actions workflow](https://github.com/gartnera/rebase-upstream-action) automatically do the rebase ([example](https://github.com/gartnera/AeroSpace/commit/baec16fcb2cfc29024ccdb0f2ef5f8c9e0ce823f)). You do need to use a personal access token pushes from github actions will not a trigger CI.

The check will start failing if the rebase fails. You could add a step which would notify you when 