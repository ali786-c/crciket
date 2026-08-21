package com.devwithguru.cricket.data.db.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import androidx.room.Update
import com.devwithguru.cricket.data.db.entity.BowlerStatsEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface BowlerStatsDao {
    @Query("SELECT * FROM bowler_stats WHERE inningsId = :inningsId")
    fun getBowlersByInnings(inningsId: Long): Flow<List<BowlerStatsEntity>>

    @Query("SELECT * FROM bowler_stats WHERE inningsId = :inningsId")
    suspend fun getBowlersByInningsSync(inningsId: Long): List<BowlerStatsEntity>

    @Query("SELECT * FROM bowler_stats WHERE inningsId = :inningsId AND name = :name")
    suspend fun getBowler(inningsId: Long, name: String): BowlerStatsEntity?

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertBowler(bowler: BowlerStatsEntity): Long

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(bowlers: List<BowlerStatsEntity>)

    @Update
    suspend fun updateBowler(bowler: BowlerStatsEntity)

    @Query("DELETE FROM bowler_stats WHERE inningsId = :inningsId")
    suspend fun deleteAllByInnings(inningsId: Long)
}
