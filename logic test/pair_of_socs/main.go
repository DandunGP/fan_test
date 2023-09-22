package main

import "fmt"

func pairSocks(socks []int) int {
	var result int

	data := make(map[int]int)

	for _, value := range socks {
		if count, exists := data[value]; exists {
			result++
			if count == 1 {
				delete(data, value)
			} else {
				data[value]--
			}
		} else {
			data[value] = 1
		}
	}

	return result
}

func main() {
	fmt.Println(pairSocks([]int{5, 7, 7, 9, 10, 4, 5, 10, 6, 5}))
	fmt.Println(pairSocks([]int{6, 5, 2, 3, 5, 2, 2, 1, 1, 5, 1, 3, 3, 3, 5}))
	fmt.Println(pairSocks([]int{1, 1, 3, 1, 2, 1, 3, 3, 3, 3}))
}
