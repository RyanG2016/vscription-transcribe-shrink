21JUL2021

**Note: Works with DSS and DS2 files

- Run Installer
- Overwrite exe from installer with exe in folder

[Usage] :
DSSConverterCLI.exe
                -convert <filepath>
                -outputfolder <folder>
                -format <.mp3 or .wav>
                -overwrite <always/no>
                -channels <1 mono / 2 stereo>
                -samplesPerSec <sample per sec value>
                -bitsPerSample <bits per sample>

example: -channels 1 -samplesPerSec 16000 -bitsPerSample 8
 DSSConverterCLI.exe -convert <infile> -outputfolder <uploads> -format .wav -overwrite always -channels 1 -samplesPerSec 16000 -bitsPerSample 8

Note:
When using mp3, you need to refer to the available_bitrates.txt file in this directory to see what sampleRates and channels can output which mp3 bitrate.

For sample, if you want a 128KB bitrate mp3, you need to specify and sampleRate of 32000, 44100 or 48000 (mono) only. If you try to use a different sample rate, the conversion will fail.

During audiofile quality testing, I didn't notice any difference between a 44100 or 32000 sample rate with an output bitrate of 128kbps to a 64 kbps file. The filesize was half and I ran
both files through rev.ai and they both came back with the same recognition accuracy.
In light of this, we have configured the ds2 conversion to use the following settings;

        private $conv_ext = ".mp3",
        private $channels = "1",
        private $samplesPerSec = "44100",
        private $bitsPerSec = "64"

This creates a conversion command similar to the following;
 DSSConverterCLI.exe -convert <infile> -outputfolder <uploads> -format .mp3 -overwrite always -channels 1 -samplesPerSec 44100 -bitsPerSample 64