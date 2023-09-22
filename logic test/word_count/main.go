package main

import (
	"fmt"
	"strings"
)

func wordCount(word string) int {
	var result int
	specialCharacterAllow := "~!@#$%^&*()_+=`[]{};:<>/"

	splitWord := strings.Split(word, " ")

	for _, oneWord := range splitWord {
		isSpecial := false

		for _, char := range oneWord {
			if strings.ContainsRune(specialCharacterAllow, char) {
				isSpecial = true
				break
			}
		}

		if !isSpecial {
			result++
		}
	}

	return result
}

func main() {
	fmt.Println(wordCount("Kemarin sore sophi[a ke mall."))
	fmt.Println(wordCount("Saat meng*ecat tembok, Agung dib_antu oleh Raihan."))
	fmt.Println(wordCount("Berapa u(mur minimal[ untuk !mengurus ktp?"))
	fmt.Println(wordCount("Masing-masing anak mendap(atkan uang jajan ya=ng be&rbeda."))
}
