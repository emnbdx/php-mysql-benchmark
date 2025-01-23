# php-mysql-benchmark
🔄 Insert, 📖 Read, ✍️ Write, 📊 Display result : simple, basic ✅

Setup config on .env

```bash
cp .env.example .env
```

Then deploy to your server and wait 😎


# Result

Result with 1000 line count

| Provider    | Web size | Db size | Price | Read (req/s) | Write (req/s) |
| -------- | ------- | ------- | ------- | ------- | ------- |
| Scalingo | L | Business 2G | 153,8€ | 188,34 | 160,20 |
| Azure | B1 (1 vCore, 1,75 Go RAM) | Standard_D2ads_v5 (2 vCores, 8 Go memory, 3200 max iops) | 151,28€ | 442,14 | 419,45 |
| OVH | performance 1 (2 vCores, 4G RAM) || 10,99€ | 2925,11 | 2359,06 |
| Clever Cloud   | XS instance (1G, 1 CPUs) | S Small Space (2G) | 66€ | 3140,63 | 2860,09 |
| Local | Apple M2 Pro ||| 34743,78 | 29625,18 |